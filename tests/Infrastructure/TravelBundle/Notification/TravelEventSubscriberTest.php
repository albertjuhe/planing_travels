<?php

namespace App\Tests\Infrastructure\TravelBundle\Notification;

use App\Domain\Travel\Events\TravelWasPublished;
use App\Domain\Travel\Events\TravelWasAdded;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\TravelRepository;
use App\Infrastructure\TravelBundle\Notification\TravelEventSubscriber;
use App\Tests\Domain\Travel\Model\TravelMother;
use PHPUnit\Framework\TestCase;

class TravelEventSubscriberTest extends TestCase
{
    private $travelRepository;
    private TravelEventSubscriber $subscriber;

    public function setUp(): void
    {
        $this->travelRepository = $this->createMock(TravelRepository::class);
        $this->subscriber = new TravelEventSubscriber($this->travelRepository);
    }

    public function testIsSubscribedToTravelWasPublished(): void
    {
        $event = new TravelWasPublished(['id' => 'abc'], 1);

        $this->assertTrue($this->subscriber->isSubscribedTo($event));
    }

    public function testIsSubscribedToTravelWasAdded(): void
    {
        $event = $this->createMock(TravelWasAdded::class);

        $this->assertTrue($this->subscriber->isSubscribedTo($event));
    }

    public function testHandlePublishedTravelLoadsTravel(): void
    {
        $travel = TravelMother::random();
        $travel->publish();
        $travelData = ['id' => $travel->getId()->id()];

        $this->travelRepository
            ->expects($this->once())
            ->method('ofIdOrFail')
            ->with($travel->getId()->id())
            ->willReturn($travel);

        $event = new TravelWasPublished($travelData, 1);
        $this->subscriber->handle($event);
    }

    public function testHandleTravelWasAddedDoesNothing(): void
    {
        $this->travelRepository->expects($this->never())->method('ofIdOrFail');

        $event = $this->createMock(TravelWasAdded::class);
        $this->subscriber->handle($event);
    }
}
