<?php

namespace App\Infrastructure\WebSocket;

use Psr\Log\LoggerInterface;

class WebSocketNotifier
{
    /** @var string */
    private $wsServerUrl;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(string $wsServerUrl, LoggerInterface $logger)
    {
        $this->wsServerUrl = rtrim($wsServerUrl, '/');
        $this->logger = $logger;
    }

    public function notifyLocationAdded(string $travelId, array $locationData): void
    {
        $this->broadcast($travelId, [
            'event'    => 'location_added',
            'travelId' => $travelId,
            'location' => $locationData,
        ]);
    }

    public function notifyLocationRemoved(string $travelId, string $locationId, string $byUserId, string $byUsername): void
    {
        $this->broadcast($travelId, [
            'event'      => 'location_removed',
            'travelId'   => $travelId,
            'locationId' => $locationId,
            'byUserId'   => $byUserId,
            'byUsername'  => $byUsername,
        ]);
    }

    public function notifyLocationUpdated(string $travelId, array $locationData, string $byUserId, string $byUsername): void
    {
        $this->broadcast($travelId, [
            'event'    => 'location_updated',
            'travelId' => $travelId,
            'location' => $locationData,
            'byUserId' => $byUserId,
            'byUsername' => $byUsername,
        ]);
    }

    public function notifyVisitDateChanged(string $travelId, string $locationId, ?string $visitAt, string $byUserId, string $byUsername): void
    {
        $this->broadcast($travelId, [
            'event'      => 'visit_date_changed',
            'travelId'   => $travelId,
            'locationId' => $locationId,
            'visitAt'    => $visitAt,
            'byUserId'   => $byUserId,
            'byUsername'  => $byUsername,
        ]);
    }

    public function notifyVisitDatesChanged(string $travelId, string $locationId, array $visitDates, string $byUserId, string $byUsername): void
    {
        $this->broadcast($travelId, [
            'event'      => 'visit_dates_changed',
            'travelId'   => $travelId,
            'locationId' => $locationId,
            'visitDates' => $visitDates,
            'byUserId'   => $byUserId,
            'byUsername'  => $byUsername,
        ]);
    }

    public function notifyImageUploaded(string $travelId, string $locationId, string $filename, string $byUserId, string $byUsername): void
    {
        $this->broadcast($travelId, [
            'event'      => 'image_uploaded',
            'travelId'   => $travelId,
            'locationId' => $locationId,
            'filename'   => $filename,
            'byUserId'   => $byUserId,
            'byUsername'  => $byUsername,
        ]);
    }

    public function notifyNoteAdded(string $travelId, string $locationId, int $noteId, string $content, string $byUserId, string $byUsername): void
    {
        $this->broadcast($travelId, [
            'event'      => 'note_added',
            'travelId'   => $travelId,
            'locationId' => $locationId,
            'noteId'     => $noteId,
            'content'    => $content,
            'byUserId'   => $byUserId,
            'byUsername'  => $byUsername,
        ]);
    }

    public function notifyNoteDeleted(string $travelId, string $locationId, int $noteId, string $byUserId, string $byUsername): void
    {
        $this->broadcast($travelId, [
            'event'      => 'note_deleted',
            'travelId'   => $travelId,
            'locationId' => $locationId,
            'noteId'     => $noteId,
            'byUserId'   => $byUserId,
            'byUsername'  => $byUsername,
        ]);
    }

    public function sendChatMessage(string $travelId, string $userId, string $username, string $content): void
    {
        $this->broadcast($travelId, [
            'type'     => 'chat',
            'userId'   => $userId,
            'username' => $username,
            'content'  => $content,
            'time'     => date('c'),
        ]);
    }

    protected function broadcast(string $travelId, array $payload): void
    {
        $url = $this->wsServerUrl.'/travel/'.$travelId.'/broadcast';
        $body = json_encode($payload);

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\nContent-Length: ".strlen($body)."\r\n",
                'content' => $body,
                'timeout' => 2,
                'ignore_errors' => true,
            ],
        ]);

        try {
            @file_get_contents($url, false, $context);
        } catch (\Throwable $e) {
            $this->logger->warning('WebSocketNotifier: failed to notify room '.$travelId.': '.$e->getMessage());
        }
    }
}
