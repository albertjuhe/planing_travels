<?php

namespace App\Tests\Application\UseCases\Journal;

use App\Application\Command\Journal\UpdateJournalEntryCommand;
use App\Application\UseCases\Journal\UpdateJournalEntryService;
use App\Domain\Journal\Model\JournalEntry;
use App\Domain\Journal\Repository\JournalEntryRepository;
use App\Tests\Domain\Journal\Model\JournalEntryMother;
use App\Tests\Domain\User\Model\UserMother;
use PHPUnit\Framework\TestCase;

class UpdateJournalEntryServiceTest extends TestCase
{
    public function testHappyPathUpdatesContentAndTitle(): void
    {
        $author = UserMother::random();
        $entry = JournalEntryMother::forTravel(
            \App\Tests\Domain\Travel\Model\TravelMother::random(),
            $author,
            new \DateTime('2024-06-15')
        );

        $journalRepo = $this->createMock(JournalEntryRepository::class);
        $journalRepo->method('findById')->willReturn($entry);
        $journalRepo->expects($this->once())->method('save');

        $service = new UpdateJournalEntryService($journalRepo);

        $command = new UpdateJournalEntryCommand(
            $entry->getId(),
            $author->getId()->id(),
            'Updated content',
            'Updated title',
            JournalEntry::MOOD_RELAXED
        );

        $service($command);

        $this->assertSame('Updated content', $entry->getContent());
        $this->assertSame('Updated title', $entry->getTitle());
        $this->assertSame(JournalEntry::MOOD_RELAXED, $entry->getMood());
    }

    public function testNonAuthorCannotEdit(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Not allowed to edit this journal entry.');

        $author = UserMother::random();
        $stranger = UserMother::random();
        $entry = JournalEntryMother::forTravel(
            \App\Tests\Domain\Travel\Model\TravelMother::random(),
            $author,
            new \DateTime('2024-06-15')
        );

        $journalRepo = $this->createMock(JournalEntryRepository::class);
        $journalRepo->method('findById')->willReturn($entry);

        $service = new UpdateJournalEntryService($journalRepo);

        $command = new UpdateJournalEntryCommand(
            $entry->getId(),
            $stranger->getId()->id(),
            'Hacked content'
        );

        $service($command);
    }

    public function testEntryNotFoundThrows(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Journal entry not found.');

        $journalRepo = $this->createMock(JournalEntryRepository::class);
        $journalRepo->method('findById')->willReturn(null);

        $service = new UpdateJournalEntryService($journalRepo);

        $command = new UpdateJournalEntryCommand('nonexistent-id', 1, 'content');

        $service($command);
    }
}
