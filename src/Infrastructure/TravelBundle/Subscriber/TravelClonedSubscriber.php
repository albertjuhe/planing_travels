<?php

namespace App\Infrastructure\TravelBundle\Subscriber;

use App\Domain\Travel\Events\TravelWasCloned;
use App\Infrastructure\WebSocket\WebSocketNotifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TravelClonedSubscriber implements EventSubscriberInterface
{
    private WebSocketNotifier $notifier;

    public function __construct(WebSocketNotifier $notifier)
    {
        $this->notifier = $notifier;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TravelWasCloned::class => 'onTravelCloned',
        ];
    }

    public function onTravelCloned(TravelWasCloned $event): void
    {
        $this->notifier->broadcast($event->getSourceTravelId(), [
            'type' => 'travel.cloned',
            'sourceTravelId' => $event->getSourceTravelId(),
            'targetTravelId' => $event->getTargetTravelId(),
            'clonedByUserId' => $event->getClonedByUserId(),
            'sourceTitleSnapshot' => $event->getSourceTitleSnapshot(),
        ]);
    }
}
