<?php

namespace App\Tests\Domain\Travel\Events;

use App\Domain\Travel\Events\TravelWasCloned;
use PHPUnit\Framework\TestCase;

class TravelWasClonedTest extends TestCase
{
    public function testConstructorAssignsAllFields(): void
    {
        $event = new TravelWasCloned('target-uuid', 'source-uuid', 2, 1, 'Scotland Highlands');

        $this->assertSame('target-uuid', $event->getTargetTravelId());
        $this->assertSame('source-uuid', $event->getSourceTravelId());
        $this->assertSame(2, $event->getClonedByUserId());
        $this->assertSame(1, $event->getSourceUserId());
        $this->assertSame('Scotland Highlands', $event->getSourceTitleSnapshot());
        $this->assertInstanceOf(\DateTime::class, $event->occurredOn());
    }

    public function testOccurredOnIsSetToNow(): void
    {
        $before = new \DateTime();
        $event = new TravelWasCloned('t', 's', 2, 1, 'Title');
        $after = new \DateTime();

        $this->assertGreaterThanOrEqual($before, $event->occurredOn());
        $this->assertLessThanOrEqual($after, $event->occurredOn());
    }
}
