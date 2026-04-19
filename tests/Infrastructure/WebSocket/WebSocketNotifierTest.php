<?php

namespace App\Tests\Infrastructure\WebSocket;

use App\Infrastructure\WebSocket\WebSocketNotifier;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class WebSocketNotifierTest extends TestCase
{
    private $logger;

    public function setUp()
    {
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testNotifyLocationAddedBuildsCorrectPayload(): void
    {
        $travelId = 'travel-uuid-1';
        $locationData = ['id' => 'loc-1', 'title' => 'Paris', 'latitude' => 48.8, 'longitude' => 2.3, 'addedByUserId' => '42'];

        $notifier = $this->getMockBuilder(WebSocketNotifier::class)
            ->setConstructorArgs(['http://localhost:5555', $this->logger])
            ->setMethods(['broadcast'])
            ->getMock();

        $notifier->expects($this->once())
            ->method('broadcast')
            ->with($travelId, [
                'event'    => 'location_added',
                'travelId' => $travelId,
                'location' => $locationData,
            ]);

        $notifier->notifyLocationAdded($travelId, $locationData);
    }

    public function testNotifyLocationRemovedBuildsCorrectPayload(): void
    {
        $travelId = 'travel-uuid-1';
        $locationId = 'loc-uuid-1';
        $byUserId = '42';

        $notifier = $this->getMockBuilder(WebSocketNotifier::class)
            ->setConstructorArgs(['http://localhost:5555', $this->logger])
            ->setMethods(['broadcast'])
            ->getMock();

        $notifier->expects($this->once())
            ->method('broadcast')
            ->with($travelId, [
                'event'      => 'location_removed',
                'travelId'   => $travelId,
                'locationId' => $locationId,
                'byUserId'   => $byUserId,
                'byUsername'  => 'johndoe',
            ]);

        $notifier->notifyLocationRemoved($travelId, $locationId, $byUserId, 'johndoe');
    }

    public function testNotifyLocationUpdatedBuildsCorrectPayload(): void
    {
        $travelId = 'travel-uuid-1';
        $byUserId = '42';
        $locationData = ['id' => 'loc-1', 'title' => 'Rome', 'url' => '', 'description' => 'Nice city', 'updatedByUserId' => $byUserId];

        $notifier = $this->getMockBuilder(WebSocketNotifier::class)
            ->setConstructorArgs(['http://localhost:5555', $this->logger])
            ->setMethods(['broadcast'])
            ->getMock();

        $notifier->expects($this->once())
            ->method('broadcast')
            ->with($travelId, [
                'event'    => 'location_updated',
                'travelId' => $travelId,
                'location' => $locationData,
                'byUserId' => $byUserId,
                'byUsername' => 'johndoe',
            ]);

        $notifier->notifyLocationUpdated($travelId, $locationData, $byUserId, 'johndoe');
    }

    public function testNotifyLocationRemovedDoesNotThrowWhenServerUnreachable(): void
    {
        $notifier = new WebSocketNotifier('http://127.0.0.1:19999', $this->logger);

        $this->logger->expects($this->never())->method('warning');

        // Should not throw — broadcast catches all errors silently
        $notifier->notifyLocationRemoved('travel-1', 'loc-1', '1', 'testuser');

        $this->assertTrue(true);
    }
}
