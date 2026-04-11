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

    public function notifyLocationRemoved(string $travelId, string $locationId, string $byUserId): void
    {
        $this->broadcast($travelId, [
            'event'      => 'location_removed',
            'travelId'   => $travelId,
            'locationId' => $locationId,
            'byUserId'   => $byUserId,
        ]);
    }

    public function notifyLocationUpdated(string $travelId, array $locationData, string $byUserId): void
    {
        $this->broadcast($travelId, [
            'event'    => 'location_updated',
            'travelId' => $travelId,
            'location' => $locationData,
            'byUserId' => $byUserId,
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
