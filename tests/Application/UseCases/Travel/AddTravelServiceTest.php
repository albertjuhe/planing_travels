<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Domain\Event\DomainEventPublisher;
use App\Domain\Travel\Events\TravelWasAdded;
use App\Domain\User\Exceptions\UserDoesntExists;
use App\Application\UseCases\Travel\AddTravelService;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Application\Command\Travel\AddTravelCommand;

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
        $userId = mt_rand();
        $user = User::byId($userId);

        $travel = new Travel();
        $travelId = $travel->getId()->id();

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
        $user = User::byId(0);

        $addTravelService = new AddTravelService($this->travelRepository, $this->userRepository);
        $command = new AddTravelCommand($travel, $user);
        $addTravelService->handle($command);
    }
}
