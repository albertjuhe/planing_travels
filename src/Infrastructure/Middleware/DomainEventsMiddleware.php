<?php

namespace App\Infrastructure\Middleware;

use App\Domain\Event\DomainEventPublisher;
use App\Domain\Event\PersistDomainEventSubscriber;
use App\Domain\Event\Repository\EventStore;
use App\Domain\Travel\Repository\TravelRepository;
use App\EventSubscriber\SymfonyEventSubscriber;
use App\Infrastructure\TravelBundle\Notification\TravelEventSubscriber;
use League\Tactician\Middleware;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DomainEventsMiddleware implements Middleware
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

    public function execute($command, callable $next)
    {
        $returnValue = $next($command);

        return $returnValue;
    }
}
