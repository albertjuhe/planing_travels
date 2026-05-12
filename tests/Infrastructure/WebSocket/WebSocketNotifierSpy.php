<?php

namespace App\Tests\Infrastructure\WebSocket;

use App\Infrastructure\WebSocket\WebSocketNotifier;
use Psr\Log\NullLogger;

class WebSocketNotifierSpy extends WebSocketNotifier
{
    public array $broadcasts = [];

    public function __construct()
    {
        parent::__construct('http://localhost:5555', new NullLogger());
    }

    public function broadcast(string $travelId, array $payload): void
    {
        $this->broadcasts[] = ['travelId' => $travelId, 'payload' => $payload];
    }
}
