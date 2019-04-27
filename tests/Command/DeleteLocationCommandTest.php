<?php

namespace App\Tests\Command;

use App\Application\Command\Location\DeleteLocationCommand;
use PHPUnit\Framework\TestCase;

class DeleteLocationCommandTest extends TestCase
{
    public function testDelelocationCommand()
    {
        $locationId = mt_rand();
        $deleteLocationCommand = new DeleteLocationCommand($locationId);
        $this->assertEquals($locationId, $deleteLocationCommand->getLocationId());
    }
}
