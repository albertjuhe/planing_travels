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
    private $commandTester;

    public function setUp(): void
    {
        $this->populateIndexer = $this->createMock(PopulateIndexer::class);

        $command = new PopulateTravelsToElasticSearchCommand($this->populateIndexer);
        $application = new Application();
        $application->add($command);

        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteCallsPopulateIndexer(): void
    {
        $this->populateIndexer->expects($this->once())->method('execute');

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testCommandNameIsCorrect(): void
    {
        $application = new Application();
        $command = new PopulateTravelsToElasticSearchCommand($this->populateIndexer);
        $application->add($command);

        $this->assertSame('app:populate-travel-elasticsearch', $command->getName());
    }

    public function testExecuteReturnsSuccessCode(): void
    {
        $this->populateIndexer->method('execute')->willReturn(null);

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    protected function tearDown(): void
    {
        $this->populateIndexer = null;
    }
}
