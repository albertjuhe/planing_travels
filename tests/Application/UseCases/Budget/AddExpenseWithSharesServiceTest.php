<?php

namespace App\Tests\Application\UseCases\Budget;

use App\Application\Command\Budget\AddExpenseWithSharesCommand;
use App\Application\Service\TravelAuthorizationService;
use App\Application\UseCases\Budget\AddExpenseWithSharesService;
use App\Domain\Budget\Model\TravelExpense;
use App\Domain\Money\Service\ExchangeRateProvider;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use App\Infrastructure\UserBundle\Repository\DoctrineUserRepository;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class AddExpenseWithSharesServiceTest extends TestCase
{
    private function makeUserRepo(array $usersById): DoctrineUserRepository
    {
        $repo = $this->getMockBuilder(DoctrineUserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['ofIdOrFail'])
            ->getMock();
        $repo->method('ofIdOrFail')->willReturnCallback(function ($userId) use ($usersById) {
            $id = (string) (($userId instanceof \App\Domain\User\ValueObject\UserId) ? $userId->id() : $userId);
            return $usersById[$id] ?? array_values($usersById)[0];
        });

        return $repo;
    }
    private function buildService(
        Travel $travel,
        User $requester,
        bool $canEdit = true,
        ?User $payer = null,
        ?array $extraUsers = [],
        ?ExchangeRateProvider $exchangeRateProvider = null
    ): array {
        $travelRepo = $this->createMock(DoctrineTravelRepository::class);
        $travelRepo->method('ofIdOrFail')->willReturn($travel);

        $payer = $payer ?? $requester;
        $usersById = [(string) $requester->getId()->id() => $requester, (string) $payer->getId()->id() => $payer];
        foreach ($extraUsers as $u) {
            $usersById[(string) $u->getId()->id()] = $u;
        }
        $userRepo = $this->makeUserRepo($usersById);

        $budgetRepo = $this->createMock(EntityRepository::class);
        $budgetRepo->method('findOneBy')->willReturn(null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('persist');
        $em->method('flush');
        $em->method('getRepository')->willReturn($budgetRepo);

        $authService = $this->createMock(TravelAuthorizationService::class);
        $authService->method('canEdit')->willReturn($canEdit);

        $service = new AddExpenseWithSharesService($travelRepo, $userRepo, $em, $authService, null, $exchangeRateProvider);

        return [$service, $travelRepo, $userRepo, $em, $authService];
    }

    public function testHappyPathEqualSplitWithExplicitParticipants(): void
    {
        $owner = UserMother::random();
        $other = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($owner);

        [$service] = $this->buildService($travel, $owner, true, $owner, [$other]);

        $command = new AddExpenseWithSharesCommand(
            $travel->getId()->id(),
            $owner->getId()->id(),
            'Dinner',
            60.0,
            'EUR',
            TravelExpense::CATEGORY_FOOD,
            $owner->getId()->id(),
            TravelExpense::SPLIT_EQUAL,
            [$owner->getId()->id(), $other->getId()->id()]
        );

        $expense = $service($command);

        $this->assertInstanceOf(TravelExpense::class, $expense);
        $this->assertSame('Dinner', $expense->getDescription());
        $this->assertSame(60.0, $expense->getAmount());
        $this->assertCount(2, $expense->getShares());
    }

    public function testEqualSplitWithNoParticipantsUsesAllTravelers(): void
    {
        $owner = UserMother::random();
        $shared = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($owner);
        $travel->addShareduser($shared);

        [$service] = $this->buildService($travel, $owner, true, $owner, [$shared]);

        $command = new AddExpenseWithSharesCommand(
            $travel->getId()->id(),
            $owner->getId()->id(),
            'Hotel',
            100.0,
            'EUR',
            TravelExpense::CATEGORY_ACCOMMODATION,
            $owner->getId()->id(),
            TravelExpense::SPLIT_EQUAL,
            []
        );

        $expense = $service($command);

        $this->assertCount(2, $expense->getShares());
    }

    public function testExactSplitWithCorrectSumSucceeds(): void
    {
        $userA = UserMother::random();
        $userB = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($userA);

        [$service] = $this->buildService($travel, $userA, true, $userA, [$userB]);

        $idA = (string) $userA->getId()->id();
        $idB = (string) $userB->getId()->id();

        $command = new AddExpenseWithSharesCommand(
            $travel->getId()->id(),
            $userA->getId()->id(),
            'Taxi',
            100.0,
            'EUR',
            TravelExpense::CATEGORY_TRANSPORT,
            $userA->getId()->id(),
            TravelExpense::SPLIT_EXACT,
            [],
            [$idA => 30.0, $idB => 70.0]
        );

        $expense = $service($command);

        $this->assertSame(TravelExpense::SPLIT_EXACT, $expense->getSplitMode());
        $this->assertCount(2, $expense->getShares());
    }

    public function testExactSplitWithWrongSumThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $userA = UserMother::random();
        $userB = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($userA);

        [$service] = $this->buildService($travel, $userA, true, $userA, [$userB]);

        $idA = (string) $userA->getId()->id();
        $idB = (string) $userB->getId()->id();

        $command = new AddExpenseWithSharesCommand(
            $travel->getId()->id(),
            $userA->getId()->id(),
            'Taxi',
            100.0,
            'EUR',
            TravelExpense::CATEGORY_TRANSPORT,
            $userA->getId()->id(),
            TravelExpense::SPLIT_EXACT,
            [],
            [$idA => 30.0, $idB => 50.0]
        );

        $service($command);
    }

    public function testWithoutPermissionThrowsRuntimeException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Not allowed to add expenses to this travel.');

        $user = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser(UserMother::random());

        [$service] = $this->buildService($travel, $user, false);

        $command = new AddExpenseWithSharesCommand(
            $travel->getId()->id(),
            $user->getId()->id(),
            'Food',
            50.0,
            'EUR',
            TravelExpense::CATEGORY_FOOD,
            $user->getId()->id(),
            TravelExpense::SPLIT_EQUAL
        );

        $service($command);
    }

    public function testMultiCurrencyUsesExchangeRateProvider(): void
    {
        $owner = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($owner);

        $exchangeProvider = $this->createMock(ExchangeRateProvider::class);
        $exchangeProvider->method('getRate')->willReturn(1.08);

        [$service] = $this->buildService($travel, $owner, true, $owner, [], $exchangeProvider);

        $command = new AddExpenseWithSharesCommand(
            $travel->getId()->id(),
            $owner->getId()->id(),
            'Flight',
            100.0,
            'USD',
            TravelExpense::CATEGORY_TRANSPORT,
            $owner->getId()->id(),
            TravelExpense::SPLIT_EQUAL,
            [$owner->getId()->id()]
        );

        $expense = $service($command);

        $this->assertEqualsWithDelta(108.0, $expense->getAmountInTravelCurrency(), 0.01);
        $this->assertEqualsWithDelta(1.08, $expense->getExchangeRateAtCreation(), 0.001);
    }

    public function testExchangeRateProviderExceptionFallsBackToOneToOne(): void
    {
        $owner = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($owner);

        $exchangeProvider = $this->createMock(ExchangeRateProvider::class);
        $exchangeProvider->method('getRate')->willThrowException(new \RuntimeException('API down'));

        [$service] = $this->buildService($travel, $owner, true, $owner, [], $exchangeProvider);

        $command = new AddExpenseWithSharesCommand(
            $travel->getId()->id(),
            $owner->getId()->id(),
            'Flight',
            100.0,
            'USD',
            TravelExpense::CATEGORY_TRANSPORT,
            $owner->getId()->id(),
            TravelExpense::SPLIT_EQUAL,
            [$owner->getId()->id()]
        );

        $expense = $service($command);

        $this->assertSame(100.0, $expense->getAmountInTravelCurrency());
    }

    public function testPersistIsCalledForExpenseAndShares(): void
    {
        $owner = UserMother::random();
        $other = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($owner);

        $travelRepo = $this->createMock(DoctrineTravelRepository::class);
        $travelRepo->method('ofIdOrFail')->willReturn($travel);

        $usersById = [(string) $owner->getId()->id() => $owner, (string) $other->getId()->id() => $other];
        $userRepo = $this->makeUserRepo($usersById);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturn(new class extends EntityRepository {
            public function __construct() {}
            public function findOneBy(array $criteria, ?array $orderBy = null): ?object { return null; }
        });
        $em->expects($this->atLeastOnce())->method('persist');
        $em->expects($this->once())->method('flush');

        $authService = $this->createMock(TravelAuthorizationService::class);
        $authService->method('canEdit')->willReturn(true);

        $service = new AddExpenseWithSharesService($travelRepo, $userRepo, $em, $authService);

        $command = new AddExpenseWithSharesCommand(
            $travel->getId()->id(),
            $owner->getId()->id(),
            'Food',
            60.0,
            'EUR',
            TravelExpense::CATEGORY_FOOD,
            $owner->getId()->id(),
            TravelExpense::SPLIT_EQUAL,
            [$owner->getId()->id(), $other->getId()->id()]
        );

        $service($command);
    }
}
