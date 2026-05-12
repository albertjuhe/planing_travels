<?php

namespace App\Tests\Application\UseCases\Journal;

use App\Application\Command\Journal\AddJournalEntryCommand;
use App\Application\UseCases\Journal\AddJournalEntryService;
use App\Domain\Journal\Model\JournalEntry;
use App\Domain\Journal\Repository\JournalEntryRepository;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\User\Repository\UserRepository;
use App\Tests\Domain\Journal\Model\JournalEntryMother;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use PHPUnit\Framework\TestCase;

class AddJournalEntryServiceTest extends TestCase
{
    private function buildService(bool $canWrite = true, ?JournalEntryRepository $journalRepo = null): array
    {
        $author = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($author);

        $travelRepo = $this->createMock(TravelRepository::class);
        $travelRepo->method('ofIdOrFail')->willReturn($travel);

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('ofIdOrFail')->willReturn($canWrite ? $author : UserMother::random());

        $journalRepo = $journalRepo ?? $this->createMock(JournalEntryRepository::class);

        $service = new AddJournalEntryService($travelRepo, $userRepo, $journalRepo);

        return [$service, $travel, $author, $journalRepo];
    }

    public function testHappyPathCreatesAndSavesEntry(): void
    {
        $savedEntries = [];
        $journalRepo = $this->createMock(JournalEntryRepository::class);
        $journalRepo->expects($this->once())->method('save')->willReturnCallback(
            function (JournalEntry $e) use (&$savedEntries) { $savedEntries[] = $e; }
        );

        [$service, $travel, $author] = $this->buildService(true, $journalRepo);

        $command = new AddJournalEntryCommand(
            $travel->getId()->id(),
            $author->getId()->id(),
            '2024-06-15',
            'Had a wonderful day.',
            'Day 1',
            JournalEntry::MOOD_HAPPY
        );

        $entry = $service($command);

        $this->assertInstanceOf(JournalEntry::class, $entry);
        $this->assertSame('Had a wonderful day.', $entry->getContent());
        $this->assertSame('Day 1', $entry->getTitle());
        $this->assertSame(JournalEntry::MOOD_HAPPY, $entry->getMood());
        $this->assertCount(1, $savedEntries);
    }

    public function testInvalidDateFormatThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        [$service, $travel, $author] = $this->buildService();

        $command = new AddJournalEntryCommand(
            $travel->getId()->id(),
            $author->getId()->id(),
            '15-06-2024',
            'Content'
        );

        $service($command);
    }

    public function testNonTravelerCannotWrite(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Not allowed to write journal for this travel.');

        $stranger = UserMother::random();
        $owner = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($owner);

        $travelRepo = $this->createMock(TravelRepository::class);
        $travelRepo->method('ofIdOrFail')->willReturn($travel);

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('ofIdOrFail')->willReturn($stranger);

        $journalRepo = $this->createMock(JournalEntryRepository::class);

        $service = new AddJournalEntryService($travelRepo, $userRepo, $journalRepo);

        $command = new AddJournalEntryCommand(
            $travel->getId()->id(),
            $stranger->getId()->id(),
            '2024-06-15',
            'Sneaking in'
        );

        $service($command);
    }

    public function testSharedUserCanWriteJournal(): void
    {
        $owner = UserMother::random();
        $shared = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($owner);
        $travel->addShareduser($shared);

        $travelRepo = $this->createMock(TravelRepository::class);
        $travelRepo->method('ofIdOrFail')->willReturn($travel);

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('ofIdOrFail')->willReturn($shared);

        $journalRepo = $this->createMock(JournalEntryRepository::class);
        $journalRepo->method('save');

        $service = new AddJournalEntryService($travelRepo, $userRepo, $journalRepo);

        $command = new AddJournalEntryCommand(
            $travel->getId()->id(),
            $shared->getId()->id(),
            '2024-06-15',
            'My entry as shared user'
        );

        $entry = $service($command);

        $this->assertInstanceOf(JournalEntry::class, $entry);
    }
}
