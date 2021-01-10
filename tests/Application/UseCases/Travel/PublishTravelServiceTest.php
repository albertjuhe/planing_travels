<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\Command\Travel\PublishTravelCommand;
use App\Application\UseCases\Travel\PublishTravelService;
use App\Application\UseCases\Travel\UpdateTravelService;
use App\Domain\Event\DomainEventPublisher;
use App\Domain\Travel\Exceptions\NotAllowedToPublishTravel;
use App\Domain\Travel\Model\Travel;
use App\Tests\Domain\User\Model\UserMother;
use App\Tests\Infrastructure\TravelBundle\Repository\InMemoryTravelRepository;
use App\Tests\Subscriber\DomainEventAllSubscriber;

class PublishTravelServiceTest extends TravelService
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testPublishTravel()
    {
        $travel = $this->travelRepository->findTravelBySlug(InMemoryTravelRepository::TRAVEL_1);
        $user = $travel->getUser();

        $this->assertEquals($travel->getStatus(), Travel::TRAVEL_DRAFT);

        /** @var PublishTravelCommand $updateTravelCommand */
        $publishTravelCommand = new PublishTravelCommand($travel->getSlug(), $user);
        /** @var UpdateTravelService */
        $publishTravelService = new PublishTravelService($this->travelRepository, $this->userRepository);
        $publishTravelService->handle($publishTravelCommand);

        $travelPublished = $this->travelRepository->getTravelById(1);
        $this->assertEquals($travelPublished->getStatus(), Travel::TRAVEL_PUBLISHED);

        /** @var DomainEventAllSubscriber */
        $subscriber = DomainEventPublisher::instance()->ofId($this->idSubscriber);
        $this->assertCount(1, $subscriber->getEvents());
    }

    public function testPublishNotAllowedException()
    {
        $this->expectException(NotAllowedToPublishTravel::class);

        $travel = $this->travelRepository->findTravelBySlug(InMemoryTravelRepository::TRAVEL_1);
        $user = UserMother::random();

        $this->assertEquals($travel->getStatus(), Travel::TRAVEL_DRAFT);

        /** @var PublishTravelCommand $updateTravelCommand */
        $publishTravelCommand = new PublishTravelCommand($travel->getSlug(), $user);
        /** @var UpdateTravelService */
        $publishTravelService = new PublishTravelService($this->travelRepository, $this->userRepository);
        $publishTravelService->handle($publishTravelCommand);
    }
}
