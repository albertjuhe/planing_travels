<?php

namespace App\Tests\Domain\Common\Model;

use App\Domain\Common\Model\AggregateRoot;
use App\Domain\Event\DomainEvent;
use PHPUnit\Framework\TestCase;

class AggregateRootTest extends TestCase
{
    public function testRecord()
    {
        $entity = new DummyEntity();
        $entity->recordEvent(new DummyEvent());
        $events = $entity->pullDomainEvents();
        $this->assertCount(1, $events);
        foreach ($events as $event) {
            $this->assertInstanceOf(DomainEvent::class, $event);
        }
    }

    public function testPullDomainEvents(): void
    {
        $numEvents = 3;

        $entity = new DummyEntity();
        foreach (range(1, $numEvents) as $num) {
            $entity->recordEvent(new DummyEvent());
        }

        $events = $entity->pullDomainEvents();
        $this->assertCount($numEvents, $events);
        foreach ($events as $event) {
            $this->assertInstanceOf(DomainEvent::class, $event);
        }
    }
}

class DummyEntity extends AggregateRoot
{
    public function recordEvent(DomainEvent $domainEvent)
    {
        $this->record($domainEvent);
    }
}

class DummyEvent implements DomainEvent
{
    public function occurredOn()
    {
        return new \DateTime();
    }
}
