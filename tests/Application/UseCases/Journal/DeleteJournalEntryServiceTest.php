<?php

namespace App\Tests\Application\UseCases\Journal;

use App\Application\Command\Journal\DeleteJournalEntryCommand;
use App\Application\UseCases\Journal\DeleteJournalEntryService;
use App\Domain\Journal\Repository\JournalEntryRepository;
use App\Tests\Domain\Journal\Model\JournalEntryMother;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use PHPUnit\Framework\TestCase;

class DeleteJournalEntryServiceTest extends TestCase
{
    public function testHappyPathCallsRemove(): void
    {
        $author = UserMother::random();
        $entry = JournalEntryMother::forTravel(TravelMother::random(), $author, new \DateTime('2024-06-15'));

        $journalRepo = $this->createMock(JournalEntryRepository::class);
        $journalRepo->method('findById')->willReturn($entry);
        $journalRepo->expects($this->once())->method('remove')->with($entry);

        $service = new DeleteJournalEntryService($journalRepo);

        $command = new DeleteJournalEntryCommand($entry->getId(), $author->getId()->id());

        $service($command);
    }

    public function testNonAuthorCannotDelete(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Not allowed to delete this journal entry.');

        $author = UserMother::random();
        $stranger = UserMother::random();
        $entry = JournalEntryMother::forTravel(TravelMother::random(), $author, new \DateTime('2024-06-15'));

        $journalRepo = $this->createMock(JournalEntryRepository::class);
        $journalRepo->method('findById')->willReturn($entry);

        $service = new DeleteJournalEntryService($journalRepo);

        $command = new DeleteJournalEntryCommand($entry->getId(), $stranger->getId()->id());

        $service($command);
    }

    public function testEntryNotFoundThrows(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Journal entry not found.');

        $journalRepo = $this->createMock(JournalEntryRepository::class);
        $journalRepo->method('findById')->willReturn(null);

        $service = new DeleteJournalEntryService($journalRepo);

        $command = new DeleteJournalEntryCommand('nonexistent-id', 1);

        $service($command);
    }
}
