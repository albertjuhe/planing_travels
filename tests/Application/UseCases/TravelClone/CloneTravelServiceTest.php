<?php

namespace App\Tests\Application\UseCases\TravelClone;

use App\Application\Command\Travel\CloneTravelCommand;
use App\Application\Command\Travel\PublishTravelCommand;
use App\Application\UseCases\Travel\CloneTravelService;
use App\Application\UseCases\Travel\PublishTravelService;
use App\Domain\Event\DomainEventPublisher;
use App\Domain\Location\Model\Location;
use App\Domain\Travel\Events\TravelWasCloned;
use App\Domain\Travel\Model\Travel;
use App\Domain\Note\Model\Note;
use App\Infrastructure\UserBundle\Repository\InMemoryUserRepository;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use App\Tests\Infrastructure\TravelBundle\Repository\InMemoryTravelRepository;
use App\Tests\Infrastructure\TravelCloneBundle\Repository\InMemoryTravelCloneRepository;
use App\Tests\Subscriber\DomainEventAllSubscriber;
use PHPUnit\Framework\TestCase;

class CloneTravelServiceTest extends TestCase
{
    /** @var InMemoryTravelRepository */
    private $travelRepository;
    /** @var InMemoryUserRepository */
    private $userRepository;
    /** @var InMemoryTravelCloneRepository */
    private $travelCloneRepository;
    /** @var int */
    private $idSubscriber;

    protected function setUp(): void
    {
        $this->travelRepository = new InMemoryTravelRepository();
        $this->userRepository = new InMemoryUserRepository();
        $this->travelCloneRepository = new InMemoryTravelCloneRepository();
        $this->travelRepository->loadData();
        $this->idSubscriber = DomainEventPublisher::instance()->subscribe(new DomainEventAllSubscriber());
    }

    protected function tearDown(): void
    {
        DomainEventPublisher::instance()->unsubscribe($this->idSubscriber);
    }

    public function testClonePublishedTravel(): void
    {
        $originalTravel = $this->travelRepository->findTravelBySlug(InMemoryTravelRepository::TRAVEL_1);
        $originalUser = $originalTravel->getUser();
        $cloningUser = UserMother::random();

        $this->publishTravel($originalTravel, $originalUser);

        $this->assertTrue($originalTravel->isPublished());

        $service = new CloneTravelService(
            $this->travelRepository,
            $this->userRepository,
            $this->travelCloneRepository
        );

        $command = new CloneTravelCommand($originalTravel->getSlug(), $cloningUser);
        $clonedTravel = $service->__invoke($command);

        $this->assertNotNull($clonedTravel);
        $this->assertNotEquals($originalTravel->getId()->id(), $clonedTravel->getId()->id());
        $this->assertEquals($originalTravel->getTitle(), $clonedTravel->getTitle());
        $this->assertEquals($originalTravel->getDescription(), $clonedTravel->getDescription());
        $this->assertEquals(Travel::TRAVEL_DRAFT, $clonedTravel->getStatus());
        $this->assertEquals($cloningUser->getId()->id(), $clonedTravel->getUser()->getId()->id());

        $clones = $this->travelCloneRepository->getAll();
        $this->assertCount(1, $clones);

        $travelClone = $clones[0];
        $this->assertEquals($originalTravel->getId()->id(), $travelClone->getOriginalTravelId());
        $this->assertEquals($clonedTravel->getId()->id(), $travelClone->getClonedTravelId());
        $this->assertEquals($cloningUser->getId()->id(), $travelClone->getClonedById());
        $this->assertEquals($originalUser->getId()->id(), $travelClone->getOriginalUserId());
        $this->assertEquals($originalTravel->getTitle(), $travelClone->getOriginalTravelTitle());

        /** @var DomainEventAllSubscriber $subscriber */
        $subscriber = DomainEventPublisher::instance()->ofId($this->idSubscriber);
        $events = $subscriber->getEvents();
        $this->assertCount(2, $events);
        $this->assertInstanceOf(TravelWasCloned::class, $events[1]);
    }

    public function testCloneWithLocationsAndVisitDates(): void
    {
        $originalTravel = $this->createTravelWithLocations();
        $originalUser = $originalTravel->getUser();
        $this->publishTravel($originalTravel, $originalUser);

        $this->assertCount(1, $originalTravel->getLocation());
        $originalLocation = $originalTravel->getLocation()->first();
        $this->assertNotNull($originalLocation->getVisitDates());
        $this->assertGreaterThan(0, $originalLocation->getVisitDates()->count());

        $cloningUser = UserMother::random();
        $service = new CloneTravelService(
            $this->travelRepository,
            $this->userRepository,
            $this->travelCloneRepository
        );

        $command = new CloneTravelCommand($originalTravel->getSlug(), $cloningUser);
        $clonedTravel = $service->__invoke($command);

        $this->assertCount(1, $clonedTravel->getLocation());
        $clonedLocation = $clonedTravel->getLocation()->first();
        $this->assertEquals($originalLocation->getTitle(), $clonedLocation->getTitle());
        $this->assertEquals($originalLocation->getDescription(), $clonedLocation->getDescription());
        $this->assertEquals(
            $originalLocation->getVisitDates()->count(),
            $clonedLocation->getVisitDates()->count()
        );
        $this->assertEquals(
            $originalLocation->getVisitDates()->first()->getVisitDateString(),
            $clonedLocation->getVisitDates()->first()->getVisitDateString()
        );

        $clones = $this->travelCloneRepository->getAll();
        $this->assertCount(1, $clones);
        $this->assertEquals($originalTravel->getId()->id(), $clones[0]->getOriginalTravelId());
    }

    public function testCloneWithNotes(): void
    {
        $originalTravel = $this->createTravelWithNotes();
        $originalUser = $originalTravel->getUser();
        $this->publishTravel($originalTravel, $originalUser);

        $originalLocation = $originalTravel->getLocation()->first();
        $originalNotes = $originalLocation->getNotas();
        $this->assertCount(1, $originalNotes);

        $cloningUser = UserMother::random();
        $service = new CloneTravelService(
            $this->travelRepository,
            $this->userRepository,
            $this->travelCloneRepository
        );

        $command = new CloneTravelCommand($originalTravel->getSlug(), $cloningUser);
        $clonedTravel = $service->__invoke($command);

        $clonedLocation = $clonedTravel->getLocation()->first();
        $clonedNotes = $clonedLocation->getNotas();
        $this->assertCount(1, $clonedNotes);
        $this->assertEquals($originalNotes->first()->getTitle(), $clonedNotes->first()->getTitle());
        $this->assertEquals($originalNotes->first()->getDescription(), $clonedNotes->first()->getDescription());
    }

    public function testCloneNonPublishedTravelThrowsException(): void
    {
        $travel = $this->travelRepository->findTravelBySlug(InMemoryTravelRepository::TRAVEL_2);
        $user = UserMother::random();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot clone a travel that is not published');

        $service = new CloneTravelService(
            $this->travelRepository,
            $this->userRepository,
            $this->travelCloneRepository
        );

        $command = new CloneTravelCommand($travel->getSlug(), $user);
        $service->__invoke($command);
    }

    private function publishTravel(Travel $travel, $user): void
    {
        $publishCommand = new PublishTravelCommand($travel->getSlug(), $user);
        $publishService = new PublishTravelService($this->travelRepository, $this->userRepository);
        $publishService->__invoke($publishCommand);
    }

    private function createTravelWithLocations(): Travel
    {
        $travel = TravelMother::random();
        $travel->setSlug($travel->getTitle());

        $location = new Location();
        $location->setTitle('Test Location');
        $location->setDescription('Test Description');
        $location->setUrl('https://example.com');
        $location->setTravel($travel);

        $location->addVisitDate(new \DateTime('2026-06-01'));
        $location->addVisitDate(new \DateTime('2026-06-02'));

        $travel->getLocation()->add($location);

        $this->travelRepository->save($travel);

        return $travel;
    }

    private function createTravelWithNotes(): Travel
    {
        $travel = TravelMother::random();
        $travel->setSlug($travel->getTitle());

        $location = new Location();
        $location->setTitle('Note Location');
        $location->setDescription('Location with a note');
        $location->setTravel($travel);

        $note = new Note();
        $note->setTitle('Test Note');
        $note->setDescription('Test Note Content');
        $note->setLocation($location);

        $location->getNotas()->add($note);
        $travel->getLocation()->add($location);

        $this->travelRepository->save($travel);

        return $travel;
    }
}
