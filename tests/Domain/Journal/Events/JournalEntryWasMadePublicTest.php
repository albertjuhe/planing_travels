<?php

namespace App\Tests\Domain\Journal\Events;

use App\Domain\Journal\Events\JournalEntryWasMadePublic;
use PHPUnit\Framework\TestCase;

class JournalEntryWasMadePublicTest extends TestCase
{
    public function testConstructorAssignsAllFields(): void
    {
        $event = new JournalEntryWasMadePublic('entry-uuid', 'travel-uuid');

        $this->assertSame('entry-uuid', $event->getEntryId());
        $this->assertSame('travel-uuid', $event->getTravelId());
        $this->assertInstanceOf(\DateTime::class, $event->occurredOn());
    }

    public function testOccurredOnIsSetToNow(): void
    {
        $before = new \DateTime();
        $event = new JournalEntryWasMadePublic('e', 't');
        $after = new \DateTime();

        $this->assertGreaterThanOrEqual($before, $event->occurredOn());
        $this->assertLessThanOrEqual($after, $event->occurredOn());
    }
}
