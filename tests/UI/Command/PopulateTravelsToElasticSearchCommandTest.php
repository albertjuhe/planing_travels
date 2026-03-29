<?php

namespace App\Tests\UI\Command;

use App\Application\UseCases\Travel\PopulateIndexer;
use App\UI\Controller\Command\PopulateTravelsToElasticSearchCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class PopulateTravelsToElasticSearchCommandTest extends TestCase
{
    private $populateIndexer;
    /** @var CommandTester */
    private $commandTester;

    public function setUp()
    {
        $this->populateIndexer = $this->createMock(PopulateIndexer::class);

        $application = new Application();
        $application->add(
            new PopulateTravelsToElasticSearchCommand($this->populateIndexer, 'populateTravels')
        );
        $commandPopulate = $application->find('populateTravels');
        $this->commandTester = new CommandTester($commandPopulate);
    }

    public function testExecutePopulate()
    {
        $this->populateIndexer->expects($this->once())->method('execute');

        $this->commandTester->execute([]);
    }

    protected function tearDown()
    {
        $this->populateIndexer = null;
        $this->commandTester = null;
    }
}
