<?php

namespace App\Infrastructure\Middleware;

use App\Domain\Event\DomainEventPublisher;
use App\Domain\Event\PersistDomainEventSubscriber;
use App\Domain\Event\Repository\EventStore;
use League\Tactician\Middleware;

class DomainEventsMiddleware implements Middleware
{
    protected $eventStore;

    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
        $persistDomainEventSubscriber = new PersistDomainEventSubscriber($this->eventStore);
        DomainEventPublisher::instance()->subscribe($persistDomainEventSubscriber);
    }

    public function execute($command, callable $next)
    {
        $returnValue = $next($command);

        return $returnValue;
    }
}
