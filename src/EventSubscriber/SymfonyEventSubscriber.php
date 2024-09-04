<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Domain\Event\DomainEvent;
use App\Domain\Event\DomainEventSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SymfonyEventSubscriber implements DomainEventSubscriber
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(DomainEvent $domainEvent): void
    {
        $this->dispatcher->dispatch($domainEvent);
    }

    public function isSubscribedTo(DomainEvent $domainEvent): bool
    {
        return true;
    }

}
