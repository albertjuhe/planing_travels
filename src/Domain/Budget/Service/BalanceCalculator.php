<?php

namespace App\Domain\Budget\Service;

class Balance
{
    public string $userId;
    public string $username;
    public float $netBalance;

    public function __construct(string $userId, string $username, float $netBalance)
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->netBalance = $netBalance;
    }
}

class SuggestedTransfer
{
    public string $fromUserId;
    public string $fromUsername;
    public string $toUserId;
    public string $toUsername;
    public float $amount;

    public function __construct(
        string $fromUserId,
        string $fromUsername,
        string $toUserId,
        string $toUsername,
        float $amount
    ) {
        $this->fromUserId = $fromUserId;
        $this->fromUsername = $fromUsername;
        $this->toUserId = $toUserId;
        $this->toUsername = $toUsername;
        $this->amount = $amount;
    }
}

class BalanceCalculator
{
    private const EPSILON = 0.01;

    /**
     * @param \App\Domain\Budget\Model\TravelExpense[] $expenses
     * @param \App\Domain\Budget\Model\Settlement[]    $settlements
     *
     * @return array{balances: Balance[], transfers: SuggestedTransfer[]}
     */
    public function calculate(array $expenses, array $settlements): array
    {
        $net = []; // userId => ['username' => ..., 'net' => float]

        foreach ($expenses as $expense) {
            $payer = $expense->getPayer();
            if (!$payer) {
                continue;
            }
            $payerId = (string) $payer->getId()->id();
            $payerName = $payer->getUsername();

            if (!isset($net[$payerId])) {
                $net[$payerId] = ['username' => $payerName, 'net' => 0.0];
            }

            $totalShares = 0.0;
            foreach ($expense->getShares() as $share) {
                if ($share->isSettled()) {
                    continue;
                }
                $debtorId = (string) $share->getDebtor()->getId()->id();
                $debtorName = $share->getDebtor()->getUsername();
                $amt = $share->getAmountInTravelCurrency();

                if (!isset($net[$debtorId])) {
                    $net[$debtorId] = ['username' => $debtorName, 'net' => 0.0];
                }

                if ($debtorId !== $payerId) {
                    $net[$payerId]['net'] += $amt;
                    $net[$debtorId]['net'] -= $amt;
                }
                $totalShares += $amt;
            }
        }

        // Apply settlements
        foreach ($settlements as $s) {
            $fromId = (string) $s->getFromUser()->getId()->id();
            $toId = (string) $s->getToUser()->getId()->id();
            $amt = $s->getAmount();

            if (!isset($net[$fromId])) {
                $net[$fromId] = ['username' => $s->getFromUser()->getUsername(), 'net' => 0.0];
            }
            if (!isset($net[$toId])) {
                $net[$toId] = ['username' => $s->getToUser()->getUsername(), 'net' => 0.0];
            }

            $net[$fromId]['net'] += $amt;
            $net[$toId]['net'] -= $amt;
        }

        $balances = [];
        foreach ($net as $userId => $data) {
            $balances[] = new Balance($userId, $data['username'], round($data['net'], 2));
        }

        $transfers = $this->minimizeTransfers($balances);

        return ['balances' => $balances, 'transfers' => $transfers];
    }

    /**
     * Greedy algorithm: pair largest creditor with largest debtor.
     *
     * @param Balance[] $balances
     *
     * @return SuggestedTransfer[]
     */
    private function minimizeTransfers(array $balances): array
    {
        $creditors = [];
        $debtors = [];

        foreach ($balances as $b) {
            if ($b->netBalance > self::EPSILON) {
                $creditors[] = ['id' => $b->userId, 'name' => $b->username, 'amount' => $b->netBalance];
            } elseif ($b->netBalance < -self::EPSILON) {
                $debtors[] = ['id' => $b->userId, 'name' => $b->username, 'amount' => -$b->netBalance];
            }
        }

        $transfers = [];

        while (!empty($creditors) && !empty($debtors)) {
            usort($creditors, fn ($a, $b) => $b['amount'] <=> $a['amount']);
            usort($debtors, fn ($a, $b) => $b['amount'] <=> $a['amount']);

            $creditor = array_shift($creditors);
            $debtor = array_shift($debtors);

            $amount = min($creditor['amount'], $debtor['amount']);
            $amount = round($amount, 2);

            if ($amount > self::EPSILON) {
                $transfers[] = new SuggestedTransfer(
                    $debtor['id'], $debtor['name'],
                    $creditor['id'], $creditor['name'],
                    $amount
                );
            }

            $creditor['amount'] = round($creditor['amount'] - $amount, 2);
            $debtor['amount'] = round($debtor['amount'] - $amount, 2);

            if ($creditor['amount'] > self::EPSILON) {
                $creditors[] = $creditor;
            }
            if ($debtor['amount'] > self::EPSILON) {
                $debtors[] = $debtor;
            }
        }

        return $transfers;
    }
}
