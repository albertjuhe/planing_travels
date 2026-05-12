<?php

namespace App\Application\UseCases\Budget;

use App\Application\UseCases\UsesCasesService;
use App\Application\Service\TravelAuthorizationService;
use App\Domain\Budget\Model\Settlement;
use App\Domain\Budget\Model\TravelExpense;
use App\Domain\Budget\Service\BalanceCalculator;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use Doctrine\ORM\EntityManagerInterface;

class CalculateBalancesService implements UsesCasesService
{
    private DoctrineTravelRepository $travelRepo;
    private EntityManagerInterface $em;
    private TravelAuthorizationService $authService;
    private BalanceCalculator $calculator;

    public function __construct(
        DoctrineTravelRepository $travelRepo,
        EntityManagerInterface $em,
        TravelAuthorizationService $authService,
        BalanceCalculator $calculator
    ) {
        $this->travelRepo = $travelRepo;
        $this->em = $em;
        $this->authService = $authService;
        $this->calculator = $calculator;
    }

    public function __invoke(string $travelId, $user): array
    {
        $travel = $this->travelRepo->ofIdOrFail($travelId);

        if (!$this->authService->canAccess($travel, $user)) {
            throw new \RuntimeException('Not allowed to view balances for this travel.');
        }

        $expenses = $this->em->getRepository(TravelExpense::class)
            ->findBy(['travel' => $travel]);

        $settlements = $this->em->getRepository(Settlement::class)
            ->findBy(['travel' => $travel]);

        $result = $this->calculator->calculate($expenses, $settlements);

        return [
            'balances' => array_map(fn ($b) => [
                'userId' => $b->userId,
                'username' => $b->username,
                'netBalance' => $b->netBalance,
            ], $result['balances']),
            'suggestedTransfers' => array_map(fn ($t) => [
                'fromUserId' => $t->fromUserId,
                'fromUsername' => $t->fromUsername,
                'toUserId' => $t->toUserId,
                'toUsername' => $t->toUsername,
                'amount' => $t->amount,
            ], $result['transfers']),
        ];
    }
}
