<?php

namespace App\Tests\Infrastructure\WebSocket;

use App\Infrastructure\WebSocket\WebSocketNotifier;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class WebSocketNotifierTest extends TestCase
{
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    private function makeSpyNotifier(): WebSocketNotifier
    {
        return new class('http://localhost:5555', $this->logger) extends WebSocketNotifier {
            public array $broadcasts = [];

            protected function broadcast(string $travelId, array $payload): void
            {
                $this->broadcasts[] = ['travelId' => $travelId, 'payload' => $payload];
            }
        };
    }

    public function testNotifyLocationAddedBuildsCorrectPayload(): void
    {
        $notifier = $this->makeSpyNotifier();
        $travelId = 'travel-uuid-1';
        $locationData = ['id' => 'loc-1', 'title' => 'Paris', 'latitude' => 48.8, 'longitude' => 2.3, 'addedByUserId' => '42'];

        $notifier->notifyLocationAdded($travelId, $locationData);

        $this->assertCount(1, $notifier->broadcasts);
        $this->assertSame($travelId, $notifier->broadcasts[0]['travelId']);
        $this->assertSame([
            'event'    => 'location_added',
            'travelId' => $travelId,
            'location' => $locationData,
        ], $notifier->broadcasts[0]['payload']);
    }

    public function testNotifyLocationRemovedBuildsCorrectPayload(): void
    {
        $notifier = $this->makeSpyNotifier();
        $travelId = 'travel-uuid-1';
        $locationId = 'loc-uuid-1';
        $byUserId = '42';

        $notifier->notifyLocationRemoved($travelId, $locationId, $byUserId, 'johndoe');

        $this->assertCount(1, $notifier->broadcasts);
        $this->assertSame([
            'event'      => 'location_removed',
            'travelId'   => $travelId,
            'locationId' => $locationId,
            'byUserId'   => $byUserId,
            'byUsername'  => 'johndoe',
        ], $notifier->broadcasts[0]['payload']);
    }

    public function testNotifyLocationUpdatedBuildsCorrectPayload(): void
    {
        $notifier = $this->makeSpyNotifier();
        $travelId = 'travel-uuid-1';
        $byUserId = '42';
        $locationData = ['id' => 'loc-1', 'title' => 'Rome', 'url' => '', 'description' => 'Nice city', 'updatedByUserId' => $byUserId];

        $notifier->notifyLocationUpdated($travelId, $locationData, $byUserId, 'johndoe');

        $this->assertCount(1, $notifier->broadcasts);
        $this->assertSame([
            'event'    => 'location_updated',
            'travelId' => $travelId,
            'location' => $locationData,
            'byUserId' => $byUserId,
            'byUsername' => 'johndoe',
        ], $notifier->broadcasts[0]['payload']);
    }

    public function testNotifyVisitDateChangedBuildsCorrectPayload(): void
    {
        $notifier = $this->makeSpyNotifier();
        $travelId = 'travel-1';
        $locationId = 'loc-1';
        $visitAt = '2026-06-01';

        $notifier->notifyVisitDateChanged($travelId, $locationId, $visitAt, '7', 'alice');

        $this->assertCount(1, $notifier->broadcasts);
        $this->assertSame([
            'event'      => 'visit_date_changed',
            'travelId'   => $travelId,
            'locationId' => $locationId,
            'visitAt'    => $visitAt,
            'byUserId'   => '7',
            'byUsername'  => 'alice',
        ], $notifier->broadcasts[0]['payload']);
    }

    public function testNotifyImageUploadedBuildsCorrectPayload(): void
    {
        $notifier = $this->makeSpyNotifier();

        $notifier->notifyImageUploaded('t-1', 'l-1', 'photo.jpg', '5', 'bob');

        $this->assertCount(1, $notifier->broadcasts);
        $this->assertSame([
            'event'      => 'image_uploaded',
            'travelId'   => 't-1',
            'locationId' => 'l-1',
            'filename'   => 'photo.jpg',
            'byUserId'   => '5',
            'byUsername'  => 'bob',
        ], $notifier->broadcasts[0]['payload']);
    }

    public function testNotifyNoteAddedBuildsCorrectPayload(): void
    {
        $notifier = $this->makeSpyNotifier();

        $notifier->notifyNoteAdded('t-1', 'l-1', 99, 'Great place!', '5', 'bob');

        $this->assertCount(1, $notifier->broadcasts);
        $this->assertSame([
            'event'      => 'note_added',
            'travelId'   => 't-1',
            'locationId' => 'l-1',
            'noteId'     => 99,
            'content'    => 'Great place!',
            'byUserId'   => '5',
            'byUsername'  => 'bob',
        ], $notifier->broadcasts[0]['payload']);
    }

    public function testNotifyNoteDeletedBuildsCorrectPayload(): void
    {
        $notifier = $this->makeSpyNotifier();

        $notifier->notifyNoteDeleted('t-1', 'l-1', 99, '5', 'bob');

        $this->assertCount(1, $notifier->broadcasts);
        $this->assertSame([
            'event'      => 'note_deleted',
            'travelId'   => 't-1',
            'locationId' => 'l-1',
            'noteId'     => 99,
            'byUserId'   => '5',
            'byUsername'  => 'bob',
        ], $notifier->broadcasts[0]['payload']);
    }

    public function testNotifyLocationRemovedDoesNotThrowWhenServerUnreachable(): void
    {
        $notifier = new WebSocketNotifier('http://127.0.0.1:19999', $this->logger);

        // Should not throw — broadcast catches all errors silently
        $notifier->notifyLocationRemoved('travel-1', 'loc-1', '1', 'testuser');

        $this->assertTrue(true);
    }
}
