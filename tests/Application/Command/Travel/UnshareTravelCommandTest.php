<?php

namespace App\Tests\Application\Command\Travel;

use App\Application\Command\Travel\UnshareTravelCommand;
use PHPUnit\Framework\TestCase;

class UnshareTravelCommandTest extends TestCase
{
    public function testGetters(): void
    {
        $command = new UnshareTravelCommand('travel-uuid-456', 7, 'janedoe');

        $this->assertSame('travel-uuid-456', $command->getTravelId());
        $this->assertSame(7, $command->getOwnerUserId());
        $this->assertSame('janedoe', $command->getTargetUsername());
    }
}
