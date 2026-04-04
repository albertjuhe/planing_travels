<?php

namespace App\Tests\Application\Command\Travel;

use App\Application\Command\Travel\ShareTravelCommand;
use PHPUnit\Framework\TestCase;

class ShareTravelCommandTest extends TestCase
{
    public function testGetters(): void
    {
        $command = new ShareTravelCommand('travel-uuid-123', 42, 'johndoe');

        $this->assertSame('travel-uuid-123', $command->getTravelId());
        $this->assertSame(42, $command->getOwnerUserId());
        $this->assertSame('johndoe', $command->getTargetUsername());
    }
}
