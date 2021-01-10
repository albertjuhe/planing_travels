<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Domain\Event\DomainEventPublisher;
use App\Domain\Travel\Events\TravelWasAdded;
use App\Domain\User\Exceptions\UserDoesntExists;
use App\Application\UseCases\Travel\AddTravelService;
use App\Domain\Travel\Model\Travel;
use App\Application\Command\Travel\AddTravelCommand;
use App\Tests\Domain\User\Model\UserMother;

class AddTravelServiceTest extends TravelService
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Add new travel.
     */
    public function testAddTravel()
    {
        $user = UserMother::random();

        $travel = new Travel();
        $travelId = $travel->getId()->id();
        $travel->setTitle('dummy1');

        $addTravelService = new AddTravelService($this->travelRepository, $this->userRepository);
        $command = new AddTravelCommand($travel, $user);
        $addTravelService->handle($command);

        $newTravel = $this->travelRepository->getTravelById($travelId);
        $this->assertEquals($newTravel->getId(), $travel->getId());

        $subscriber = DomainEventPublisher::instance()->ofId($this->idSubscriber);
        $this->assertCount(1, $subscriber->getEvents());
        $event = $subscriber->getEvents()[0];
        $this->assertInstanceOf(TravelWasAdded::class, $event);
    }

    public function testAddTravelWithInvalidUser()
    {
        $this->expectExceptionObject(new UserDoesntExists());
        $travel = new Travel();
        $user = UserMother::withUserId(0);

        $addTravelService = new AddTravelService($this->travelRepository, $this->userRepository);
        $command = new AddTravelCommand($travel, $user);
        $addTravelService->handle($command);
    }
}
