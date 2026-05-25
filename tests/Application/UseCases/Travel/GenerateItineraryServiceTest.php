<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\Command\Travel\GenerateItineraryCommand;
use App\Application\UseCases\Travel\GenerateItineraryService;
use App\Domain\Event\DomainEventPublisher;
use App\Domain\Itinerary\Exceptions\TravelHasNoDates;
use App\Domain\Itinerary\Model\ItineraryLocationInput;
use App\Domain\Itinerary\Repository\ItineraryOptimizer;
use App\Domain\Location\Model\Location;
use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\Travel\Repository\TravelRepository;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use App\Tests\Subscriber\DomainEventAllSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GenerateItineraryServiceTest extends TestCase
{
    private TravelRepository&MockObject $travelRepository;
    private ItineraryOptimizer&MockObject $optimizer;
    private EntityManagerInterface&MockObject $em;
    private \App\Infrastructure\WebSocket\WebSocketNotifier&MockObject $notifier;
    private int $idSubscriber;

    protected function setUp(): void
    {
        $this->travelRepository = $this->createMock(TravelRepository::class);
        $this->optimizer = $this->createMock(ItineraryOptimizer::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->notifier = $this->createMock(\App\Infrastructure\WebSocket\WebSocketNotifier::class);
        $this->idSubscriber = DomainEventPublisher::instance()->subscribe(new DomainEventAllSubscriber());
    }

    protected function tearDown(): void
    {
        DomainEventPublisher::instance()->unsubscribe($this->idSubscriber);
    }

    private function makeService(): GenerateItineraryService
    {
        return new GenerateItineraryService(
            $this->travelRepository,
            $this->optimizer,
            $this->em,
            $this->notifier,
        );
    }

    public function testTravelNotFoundThrowsException(): void
    {
        $this->travelRepository->method('find')->willReturn(null);
        $this->expectException(TravelDoesntExists::class);

        $command = new GenerateItineraryCommand('non-existent-id', 'user-1', 'all', 'en');
        $this->makeService()->__invoke($command);
    }

    public function testNonOwnerThrowsInvalidTravelUser(): void
    {
        $travel = TravelMother::random();
        $travel->setStartAt(new \DateTime('2026-06-01'));
        $travel->setEndAt(new \DateTime('2026-06-05'));

        $this->travelRepository->method('find')->willReturn($travel);
        $this->expectException(InvalidTravelUser::class);

        $command = new GenerateItineraryCommand(
            $travel->getId()->id(),
            'completely-different-user-id',
            'all',
            'en'
        );
        $this->makeService()->__invoke($command);
    }

    public function testTravelWithNoDatesThrowsException(): void
    {
        $user = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($user);

        $this->travelRepository->method('find')->willReturn($travel);
        $this->expectException(TravelHasNoDates::class);

        $command = new GenerateItineraryCommand(
            $travel->getId()->id(),
            $user->getId()->id(),
            'all',
            'en'
        );
        $this->makeService()->__invoke($command);
    }

    public function testNoLocationsReturnsNoOp(): void
    {
        $user = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($user);
        $travel->setStartAt(new \DateTime('2026-06-01'));
        $travel->setEndAt(new \DateTime('2026-06-03'));

        $this->travelRepository->method('find')->willReturn($travel);
        $this->optimizer->expects($this->never())->method('optimize');
        $this->em->expects($this->never())->method('flush');

        $command = new GenerateItineraryCommand(
            $travel->getId()->id(),
            $user->getId()->id(),
            'all',
            'en'
        );
        $result = $this->makeService()->__invoke($command);

        $this->assertSame(0, $result['daysAffected']);
        $this->assertSame(0, $result['locationsAssigned']);
    }

    public function testModeAllClearsExistingDatesAndAssignsNew(): void
    {
        $user = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($user);
        $start = new \DateTime('2026-06-01');
        $travel->setStartAt($start);
        $travel->setEndAt(new \DateTime('2026-06-02'));

        $loc1 = $this->makeLocation($travel, 'loc-1', 'Tirana');
        $loc1->addVisitDate(new \DateTime('2026-06-01'));
        $loc2 = $this->makeLocation($travel, 'loc-2', 'Berat');
        $travel->getLocation()->add($loc1);
        $travel->getLocation()->add($loc2);

        $this->travelRepository->method('find')->willReturn($travel);

        $plan = [1 => ['loc-1'], 2 => ['loc-2']];
        $this->optimizer->method('optimize')->willReturn($plan);
        $this->em->expects($this->once())->method('flush');

        $command = new GenerateItineraryCommand(
            $travel->getId()->id(),
            $user->getId()->id(),
            'all',
            'en'
        );
        $result = $this->makeService()->__invoke($command);

        $this->assertSame(2, $result['daysAffected']);
        $this->assertSame(2, $result['locationsAssigned']);

        $this->assertTrue($loc1->hasVisitDateOn('2026-06-01'));
        $this->assertFalse($loc1->hasVisitDateOn('2026-06-02'));
        $this->assertTrue($loc2->hasVisitDateOn('2026-06-02'));
    }

    public function testModeUnscheduledPreservesExistingAndDistributesOnlyPending(): void
    {
        $user = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($user);
        $travel->setStartAt(new \DateTime('2026-06-01'));
        $travel->setEndAt(new \DateTime('2026-06-03'));

        $scheduledLoc = $this->makeLocation($travel, 'loc-scheduled', 'Tirana');
        $scheduledLoc->addVisitDate(new \DateTime('2026-06-01'));

        $unscheduledLoc = $this->makeLocation($travel, 'loc-unscheduled', 'Gjirokastër');

        $travel->getLocation()->add($scheduledLoc);
        $travel->getLocation()->add($unscheduledLoc);

        $this->travelRepository->method('find')->willReturn($travel);

        $this->optimizer->expects($this->once())
            ->method('optimize')
            ->willReturnCallback(function (array $inputs) {
                $this->assertCount(1, $inputs);
                $this->assertSame('loc-unscheduled', $inputs[0]->id);
                return [2 => ['loc-unscheduled']];
            });

        $command = new GenerateItineraryCommand(
            $travel->getId()->id(),
            $user->getId()->id(),
            'unscheduled',
            'en'
        );
        $result = $this->makeService()->__invoke($command);

        $this->assertSame(1, $result['locationsAssigned']);
        $this->assertTrue($scheduledLoc->hasVisitDateOn('2026-06-01'), 'Scheduled location must remain unchanged');
        $this->assertTrue($unscheduledLoc->hasVisitDateOn('2026-06-02'));
    }

    public function testOptimizerReturnsUnknownIdsAreIgnored(): void
    {
        $user = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($user);
        $travel->setStartAt(new \DateTime('2026-06-01'));
        $travel->setEndAt(new \DateTime('2026-06-01'));

        $loc = $this->makeLocation($travel, 'real-id', 'Sarandë');
        $travel->getLocation()->add($loc);

        $this->travelRepository->method('find')->willReturn($travel);
        $this->optimizer->method('optimize')->willReturn([1 => ['real-id', 'fake-id-invented-by-ai']]);

        $command = new GenerateItineraryCommand(
            $travel->getId()->id(),
            $user->getId()->id(),
            'all',
            'en'
        );
        $result = $this->makeService()->__invoke($command);

        $this->assertSame(1, $result['locationsAssigned']);
        $this->assertTrue($loc->hasVisitDateOn('2026-06-01'));
    }

    public function testPositionIsSetCorrectly(): void
    {
        $user = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($user);
        $travel->setStartAt(new \DateTime('2026-06-01'));
        $travel->setEndAt(new \DateTime('2026-06-01'));

        $loc1 = $this->makeLocation($travel, 'id-first', 'Place A');
        $loc2 = $this->makeLocation($travel, 'id-second', 'Place B');
        $travel->getLocation()->add($loc1);
        $travel->getLocation()->add($loc2);

        $this->travelRepository->method('find')->willReturn($travel);
        $this->optimizer->method('optimize')->willReturn([1 => ['id-first', 'id-second']]);

        $command = new GenerateItineraryCommand(
            $travel->getId()->id(),
            $user->getId()->id(),
            'all',
            'en'
        );
        $this->makeService()->__invoke($command);

        $vd1 = $loc1->getVisitDates()->first();
        $vd2 = $loc2->getVisitDates()->first();
        $this->assertSame(0, $vd1->getPosition());
        $this->assertSame(1, $vd2->getPosition());
    }

    private function makeLocation(\App\Domain\Travel\Model\Travel $travel, string $id, string $title): Location
    {
        $loc = new Location();
        $loc->setTitle($title);
        $loc->setTravel($travel);
        $idProp = new \ReflectionProperty(Location::class, 'id');
        $idProp->setAccessible(true);
        $idProp->setValue($loc, new \App\Domain\Location\ValueObject\LocationId($id));
        return $loc;
    }
}
