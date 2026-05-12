<?php

namespace App\Tests\UI\API;

use App\Domain\Budget\Model\TravelBudget;
use App\Domain\Budget\Model\TravelExpense;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Infrastructure\LocationBundle\Repository\DoctrineLocationRepository;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use App\UI\Controller\API\BudgetAPIController;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

class BudgetAPIControllerTest extends TestCase
{
    private function buildController(Travel $travel, ?User $user = null): BudgetAPIController
    {
        $travelRepo = $this->createMock(DoctrineTravelRepository::class);
        $travelRepo->method('ofIdOrFail')->willReturn($travel);

        $locationRepo = $this->createMock(DoctrineLocationRepository::class);

        $budgetRepo = $this->createMock(EntityRepository::class);
        $budgetRepo->method('findOneBy')->willReturn(null);

        $expenseRepo = $this->createMock(EntityRepository::class);
        $expenseRepo->method('findBy')->willReturn([]);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnCallback(function (string $class) use ($budgetRepo, $expenseRepo) {
            if ($class === TravelBudget::class) {
                return $budgetRepo;
            }

            return $expenseRepo;
        });
        $em->method('persist');
        $em->method('flush');

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user ?? $travel->getUser());

        $controller = new BudgetAPIController($travelRepo, $locationRepo, $em, $security);
        $controller->setContainer($this->createMock(\Symfony\Component\DependencyInjection\ContainerInterface::class));

        return $controller;
    }

    public function testGetBudgetWithNoAuthReturns403(): void
    {
        $travel = TravelMother::random();
        $travel->setUser(UserMother::random());

        $travelRepo = $this->createMock(DoctrineTravelRepository::class);
        $locationRepo = $this->createMock(DoctrineLocationRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $controller = new BudgetAPIController($travelRepo, $locationRepo, $em, $security);
        $controller->setContainer($this->createMock(\Symfony\Component\DependencyInjection\ContainerInterface::class));

        $response = $controller->getBudget($travel->getId()->id());

        $this->assertSame(403, $response->getStatusCode());
    }

    public function testAddExpenseReturnsMissingFieldsError(): void
    {
        $owner = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($owner);

        $controller = $this->buildController($travel, $owner);

        $request = new Request([], [], [], [], [], [], json_encode(['amount' => 50.0]));
        $response = $controller->addExpense($request, $travel->getId()->id());

        $this->assertSame(400, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testAddExpenseWithNoAuthReturns403(): void
    {
        $travel = TravelMother::random();
        $travel->setUser(UserMother::random());

        $travelRepo = $this->createMock(DoctrineTravelRepository::class);
        $locationRepo = $this->createMock(DoctrineLocationRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $controller = new BudgetAPIController($travelRepo, $locationRepo, $em, $security);
        $controller->setContainer($this->createMock(\Symfony\Component\DependencyInjection\ContainerInterface::class));

        $request = new Request([], [], [], [], [], [], json_encode(['description' => 'x', 'amount' => 10.0]));
        $response = $controller->addExpense($request, $travel->getId()->id());

        $this->assertSame(403, $response->getStatusCode());
    }

    public function testDeleteExpenseWithNoAuthReturns403(): void
    {
        $travelRepo = $this->createMock(DoctrineTravelRepository::class);
        $locationRepo = $this->createMock(DoctrineLocationRepository::class);

        $em = $this->createMock(EntityManagerInterface::class);
        $expenseRepoEmpty = $this->createMock(EntityRepository::class);
        $expenseRepoEmpty->method('find')->willReturn(null);
        $em->method('getRepository')->willReturn($expenseRepoEmpty);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $controller = new BudgetAPIController($travelRepo, $locationRepo, $em, $security);
        $controller->setContainer($this->createMock(\Symfony\Component\DependencyInjection\ContainerInterface::class));

        $response = $controller->deleteExpense(999);

        $this->assertSame(403, $response->getStatusCode());
    }
}
