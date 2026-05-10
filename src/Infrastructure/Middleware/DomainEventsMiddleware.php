<?php

namespace App\Infrastructure\Middleware;

use App\Domain\Event\DomainEventPublisher;
use App\Domain\Event\PersistDomainEventSubscriber;
use App\Domain\Event\Repository\EventStore;
use App\Domain\Travel\Repository\TravelRepository;
use App\EventSubscriber\SymfonyEventSubscriber;
use App\Infrastructure\TravelBundle\Notification\TravelEventSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class DomainEventsMiddleware implements MiddlewareInterface
{
    protected $eventStore;

    public function __construct(
        EventStore $eventStore,
        EventDispatcherInterface $dispatcher,
        TravelRepository $travelRepository
    ) {
        $this->eventStore = $eventStore;

        $persistDomainEventSubscriber = new PersistDomainEventSubscriber($this->eventStore);
        DomainEventPublisher::instance()->subscribe($persistDomainEventSubscriber);

        $symfonyEventSubscriber = new SymfonyEventSubscriber($dispatcher);
        DomainEventPublisher::instance()->subscribe($symfonyEventSubscriber);

        $travelEventSubscriber = new TravelEventSubscriber($travelRepository);
        DomainEventPublisher::instance()->subscribe($travelEventSubscriber);
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        return $stack->next()->handle($envelope, $stack);
    }
}
