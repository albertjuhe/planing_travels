<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 06/11/2018
 * Time: 19:23
 */

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
    }

    public function execute($command, callable $next)
    {
        $persistDomainEventSubscriber = new PersistDomainEventSubscriber($this->eventStore);
        DomainEventPublisher::instance()->subscribe($persistDomainEventSubscriber);

        $returnValue = $next($command);

        return $returnValue;

    }

}