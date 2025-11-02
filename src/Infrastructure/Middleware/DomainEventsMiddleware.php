<?php

namespace App\Infrastructure\Middleware;

use App\Domain\Event\DomainEventPublisher;
use App\Domain\Event\PersistDomainEventSubscriber;
use App\Domain\Event\Repository\EventStore;
use App\EventSubscriber\SymfonyEventSubscriber;
use League\Tactician\Middleware;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DomainEventsMiddleware implements Middleware
{
    protected $eventStore;

    public function __construct(
        EventStore $eventStore,
        EventDispatcherInterface $dispatcher
    ) {
        $this->eventStore = $eventStore;

        $persistDomainEventSubscriber = new PersistDomainEventSubscriber($this->eventStore);
        DomainEventPublisher::instance()->subscribe($persistDomainEventSubscriber);

        $symfonyEventSubscriber = new SymfonyEventSubscriber($dispatcher);
        DomainEventPublisher::instance()->subscribe($symfonyEventSubscriber);
    }

    public function execute($command, callable $next)
    {
        $returnValue = $next($command);

        return $returnValue;
    }
}
