<?php

namespace App\Tests\Application\UseCases\Budget;

use App\Application\Service\TravelAuthorizationService;
use App\Application\UseCases\Budget\CalculateBalancesService;
use App\Domain\Budget\Model\Settlement;
use App\Domain\Budget\Model\TravelExpense;
use App\Domain\Budget\Service\BalanceCalculator;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class CalculateBalancesServiceTest extends TestCase
{
    private function buildService(bool $canAccess = true, array $expenses = [], array $settlements = []): CalculateBalancesService
    {
        $travelRepo = $this->createMock(DoctrineTravelRepository::class);
        $travelRepo->method('ofIdOrFail')->willReturn(TravelMother::random());

        $expenseRepo = $this->createMock(EntityRepository::class);
        $expenseRepo->method('findBy')->willReturn($expenses);

        $settlementRepo = $this->createMock(EntityRepository::class);
        $settlementRepo->method('findBy')->willReturn($settlements);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnCallback(function (string $class) use ($expenseRepo, $settlementRepo) {
            if ($class === TravelExpense::class) {
                return $expenseRepo;
            }
            if ($class === Settlement::class) {
                return $settlementRepo;
            }

            return $expenseRepo;
        });

        $authService = $this->createMock(TravelAuthorizationService::class);
        $authService->method('canAccess')->willReturn($canAccess);

        return new CalculateBalancesService($travelRepo, $em, $authService, new BalanceCalculator());
    }

    public function testHappyPathReturnsFormattedBalancesAndTransfers(): void
    {
        $userA = UserMother::random();
        $userA->setUsername('alice');
        $userB = UserMother::random();
        $userB->setUsername('bob');

        $travel = TravelMother::random();
        $travel->setUser($userA);

        $expense = new TravelExpense($travel, 'Dinner', 100.0, TravelExpense::CATEGORY_FOOD, 'EUR', null, null, $userA, TravelExpense::SPLIT_EQUAL, 100.0);
        $expense->splitEqually([$userA, $userB], 100.0);

        $service = $this->buildService(true, [$expense], []);

        $result = $service($travel->getId()->id(), $userA);

        $this->assertArrayHasKey('balances', $result);
        $this->assertArrayHasKey('suggestedTransfers', $result);
        $this->assertNotEmpty($result['balances']);

        $balance = $result['balances'][0];
        $this->assertArrayHasKey('userId', $balance);
        $this->assertArrayHasKey('username', $balance);
        $this->assertArrayHasKey('netBalance', $balance);
    }

    public function testWithoutAccessThrowsRuntimeException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Not allowed to view balances for this travel.');

        $service = $this->buildService(false);
        $service('some-travel-id', UserMother::random());
    }

    public function testNoExpensesReturnsEmptyBalancesAndTransfers(): void
    {
        $service = $this->buildService(true, [], []);
        $result = $service('some-travel-id', UserMother::random());

        $this->assertSame([], $result['balances']);
        $this->assertSame([], $result['suggestedTransfers']);
    }

    public function testSuggestedTransfersAreMappedCorrectly(): void
    {
        $userA = UserMother::random();
        $userA->setUsername('alice');
        $userB = UserMother::random();
        $userB->setUsername('bob');

        $travel = TravelMother::random();
        $expense = new TravelExpense($travel, 'Hotel', 100.0, TravelExpense::CATEGORY_ACCOMMODATION, 'EUR', null, null, $userA, TravelExpense::SPLIT_EQUAL, 100.0);
        $expense->splitEqually([$userA, $userB], 100.0);

        $service = $this->buildService(true, [$expense], []);
        $result = $service($travel->getId()->id(), $userA);

        $this->assertCount(1, $result['suggestedTransfers']);
        $transfer = $result['suggestedTransfers'][0];
        $this->assertArrayHasKey('fromUserId', $transfer);
        $this->assertArrayHasKey('fromUsername', $transfer);
        $this->assertArrayHasKey('toUserId', $transfer);
        $this->assertArrayHasKey('toUsername', $transfer);
        $this->assertArrayHasKey('amount', $transfer);
        $this->assertSame(50.0, $transfer['amount']);
    }
}
