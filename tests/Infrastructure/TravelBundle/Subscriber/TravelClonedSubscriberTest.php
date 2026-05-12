<?php

namespace App\Tests\Infrastructure\TravelBundle\Subscriber;

use App\Domain\Travel\Events\TravelWasCloned;
use App\Infrastructure\TravelBundle\Subscriber\TravelClonedSubscriber;
use App\Tests\Infrastructure\WebSocket\WebSocketNotifierSpy;
use PHPUnit\Framework\TestCase;

class TravelClonedSubscriberTest extends TestCase
{
    public function testOnTravelClonedBroadcastsCorrectPayload(): void
    {
        $notifier = new WebSocketNotifierSpy();
        $subscriber = new TravelClonedSubscriber($notifier);

        $event = new TravelWasCloned('target-uuid', 'source-uuid', 2, 1, 'Scotland Highlands');

        $subscriber->onTravelCloned($event);

        $this->assertCount(1, $notifier->broadcasts);
        $broadcast = $notifier->broadcasts[0];

        $this->assertSame('source-uuid', $broadcast['travelId']);
        $payload = $broadcast['payload'];
        $this->assertSame('travel.cloned', $payload['type']);
        $this->assertSame('source-uuid', $payload['sourceTravelId']);
        $this->assertSame('target-uuid', $payload['targetTravelId']);
        $this->assertSame(2, $payload['clonedByUserId']);
        $this->assertSame('Scotland Highlands', $payload['sourceTitleSnapshot']);
    }

    public function testGetSubscribedEventsIncludesTravelWasCloned(): void
    {
        $events = TravelClonedSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(TravelWasCloned::class, $events);
        $this->assertSame('onTravelCloned', $events[TravelWasCloned::class]);
    }
}
