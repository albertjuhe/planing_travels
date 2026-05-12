<?php

namespace App\Tests\Domain\Journal\Events;

use App\Domain\Journal\Events\JournalEntryWasCreated;
use PHPUnit\Framework\TestCase;

class JournalEntryWasCreatedTest extends TestCase
{
    public function testConstructorAssignsAllFields(): void
    {
        $event = new JournalEntryWasCreated('entry-uuid', 'travel-uuid', 42, '2024-06-15');

        $this->assertSame('entry-uuid', $event->getEntryId());
        $this->assertSame('travel-uuid', $event->getTravelId());
        $this->assertSame(42, $event->getAuthorId());
        $this->assertSame('2024-06-15', $event->getEntryDate());
        $this->assertInstanceOf(\DateTime::class, $event->occurredOn());
    }

    public function testOccurredOnIsSetToNow(): void
    {
        $before = new \DateTime();
        $event = new JournalEntryWasCreated('e', 't', 1, '2024-01-01');
        $after = new \DateTime();

        $this->assertGreaterThanOrEqual($before, $event->occurredOn());
        $this->assertLessThanOrEqual($after, $event->occurredOn());
    }
}
