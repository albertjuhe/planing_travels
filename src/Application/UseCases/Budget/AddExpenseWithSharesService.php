<?php

namespace App\Application\UseCases\Budget;

use App\Application\Command\Budget\AddExpenseWithSharesCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Budget\Model\TravelExpense;
use App\Domain\Budget\Service\BalanceCalculator;
use App\Domain\Money\Service\ExchangeRateProvider;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\LocationBundle\Repository\DoctrineLocationRepository;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use App\Infrastructure\UserBundle\Repository\DoctrineUserRepository;
use App\Application\Service\TravelAuthorizationService;
use Doctrine\ORM\EntityManagerInterface;

class AddExpenseWithSharesService implements UsesCasesService
{
    private DoctrineTravelRepository $travelRepo;
    private DoctrineUserRepository $userRepo;
    private ?DoctrineLocationRepository $locationRepo;
    private EntityManagerInterface $em;
    private TravelAuthorizationService $authService;
    private ?ExchangeRateProvider $exchangeRateProvider;

    public function __construct(
        DoctrineTravelRepository $travelRepo,
        DoctrineUserRepository $userRepo,
        EntityManagerInterface $em,
        TravelAuthorizationService $authService,
        ?DoctrineLocationRepository $locationRepo = null,
        ?ExchangeRateProvider $exchangeRateProvider = null
    ) {
        $this->travelRepo = $travelRepo;
        $this->userRepo = $userRepo;
        $this->em = $em;
        $this->authService = $authService;
        $this->locationRepo = $locationRepo;
        $this->exchangeRateProvider = $exchangeRateProvider;
    }

    public function __invoke(AddExpenseWithSharesCommand $command): TravelExpense
    {
        $travel = $this->travelRepo->ofIdOrFail($command->getTravelId());
        $requester = $this->userRepo->ofIdOrFail(new UserId($command->getRequesterId()));

        if (!$this->authService->canEdit($travel, $requester)) {
            throw new \RuntimeException('Not allowed to add expenses to this travel.');
        }

        $payer = $this->userRepo->ofIdOrFail(new UserId($command->getPayerUserId()));

        $expenseDate = null;
        if ($command->getExpenseDate()) {
            $expenseDate = \DateTime::createFromFormat('Y-m-d', $command->getExpenseDate()) ?: null;
        }

        $location = null;
        if ($command->getLocationId() && $this->locationRepo) {
            try {
                $location = $this->locationRepo->findById($command->getLocationId());
            } catch (\Exception $e) {
                // location optional
            }
        }

        // Resolve exchange rate
        $travelCurrency = $this->getTravelCurrency($travel);
        $expenseCurrency = strtoupper($command->getCurrency());
        $exchangeRate = 1.0;
        $amountInTravelCurrency = $command->getAmount();

        if ($expenseCurrency !== $travelCurrency && $this->exchangeRateProvider) {
            try {
                $exchangeRate = $this->exchangeRateProvider->getRate($expenseCurrency, $travelCurrency, $expenseDate);
                $amountInTravelCurrency = round($command->getAmount() * $exchangeRate, 2);
            } catch (\Throwable $e) {
                // Best-effort: use 1:1 if rate unavailable
            }
        }

        $expense = new TravelExpense(
            $travel,
            $command->getDescription(),
            $command->getAmount(),
            $command->getCategory(),
            $expenseCurrency,
            $location,
            $expenseDate,
            $payer,
            $command->getSplitMode(),
            $amountInTravelCurrency,
            $exchangeRate
        );

        // Build shares
        if ($command->getSplitMode() === TravelExpense::SPLIT_EQUAL) {
            $participantIds = $command->getParticipantIds();
            if (empty($participantIds)) {
                $participantIds = $this->getAllTravelerIds($travel);
            }
            $participants = array_map(fn ($id) => $this->userRepo->ofIdOrFail(new UserId($id)), $participantIds);
            $expense->splitEqually($participants, $amountInTravelCurrency);
        } elseif ($command->getSplitMode() === TravelExpense::SPLIT_EXACT) {
            $exactShares = $command->getExactShares();
            $sum = array_sum($exactShares);
            if (abs($sum - $command->getAmount()) > 0.02) {
                throw new \InvalidArgumentException(
                    sprintf('Exact shares sum (%.2f) must equal expense amount (%.2f).', $sum, $command->getAmount())
                );
            }
            $usersById = [];
            foreach (array_keys($exactShares) as $userId) {
                $usersById[(string) $userId] = $this->userRepo->ofIdOrFail(new UserId((int) $userId));
            }
            $expense->splitExact($exactShares, $usersById, $exchangeRate);
        }

        $this->em->persist($expense);
        foreach ($expense->getShares() as $share) {
            $this->em->persist($share);
        }
        $this->em->flush();

        return $expense;
    }

    private function getTravelCurrency(\App\Domain\Travel\Model\Travel $travel): string
    {
        $budget = $this->em->getRepository(\App\Domain\Budget\Model\TravelBudget::class)
            ->findOneBy(['travel' => $travel]);

        return $budget ? $budget->getCurrency() : 'EUR';
    }

    private function getAllTravelerIds(\App\Domain\Travel\Model\Travel $travel): array
    {
        $ids = [$travel->getUser()->getId()->id()];
        foreach ($travel->getSharedusers() as $u) {
            $ids[] = $u->getId()->id();
        }

        return $ids;
    }
}
