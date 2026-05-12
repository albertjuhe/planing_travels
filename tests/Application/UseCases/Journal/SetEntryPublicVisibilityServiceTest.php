<?php

namespace App\Tests\Application\UseCases\Journal;

use App\Application\Command\Journal\SetEntryPublicVisibilityCommand;
use App\Application\UseCases\Journal\SetEntryPublicVisibilityService;
use App\Domain\Journal\Repository\JournalEntryRepository;
use App\Tests\Domain\Journal\Model\JournalEntryMother;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use App\Tests\Infrastructure\WebSocket\WebSocketNotifierSpy;
use PHPUnit\Framework\TestCase;

class SetEntryPublicVisibilityServiceTest extends TestCase
{
    public function testSetPublicBroadcastsViaWebSocket(): void
    {
        $author = UserMother::random();
        $entry = JournalEntryMother::forTravel(TravelMother::random(), $author, new \DateTime('2024-06-15'));

        $journalRepo = $this->createMock(JournalEntryRepository::class);
        $journalRepo->method('findById')->willReturn($entry);
        $journalRepo->method('save');

        $notifier = new WebSocketNotifierSpy();

        $service = new SetEntryPublicVisibilityService($journalRepo, $notifier);

        $command = new SetEntryPublicVisibilityCommand($entry->getId(), $author->getId()->id(), true);

        $service($command);

        $this->assertTrue($entry->isPublic());
        $this->assertCount(1, $notifier->broadcasts);
        $this->assertSame('journal.entry.published', $notifier->broadcasts[0]['payload']['type']);
    }

    public function testSetPrivateDoesNotBroadcast(): void
    {
        $author = UserMother::random();
        $entry = JournalEntryMother::public();

        $journalRepo = $this->createMock(JournalEntryRepository::class);
        $journalRepo->method('findById')->willReturn($entry);
        $journalRepo->method('save');

        $notifier = new WebSocketNotifierSpy();

        $service = new SetEntryPublicVisibilityService($journalRepo, $notifier);

        $command = new SetEntryPublicVisibilityCommand($entry->getId(), $entry->getAuthor()->getId()->id(), false);

        $service($command);

        $this->assertFalse($entry->isPublic());
        $this->assertCount(0, $notifier->broadcasts);
    }

    public function testNonAuthorCannotChangeVisibility(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Not allowed to change visibility of this journal entry.');

        $author = UserMother::random();
        $stranger = UserMother::random();
        $entry = JournalEntryMother::forTravel(TravelMother::random(), $author, new \DateTime('2024-06-15'));

        $journalRepo = $this->createMock(JournalEntryRepository::class);
        $journalRepo->method('findById')->willReturn($entry);

        $notifier = new WebSocketNotifierSpy();
        $service = new SetEntryPublicVisibilityService($journalRepo, $notifier);

        $command = new SetEntryPublicVisibilityCommand($entry->getId(), $stranger->getId()->id(), true);

        $service($command);
    }

    public function testEntryNotFoundThrows(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Journal entry not found.');

        $journalRepo = $this->createMock(JournalEntryRepository::class);
        $journalRepo->method('findById')->willReturn(null);

        $notifier = new WebSocketNotifierSpy();
        $service = new SetEntryPublicVisibilityService($journalRepo, $notifier);

        $command = new SetEntryPublicVisibilityCommand('nonexistent', 1, true);

        $service($command);
    }
}
