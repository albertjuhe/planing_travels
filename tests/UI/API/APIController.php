<?php

namespace App\Tests\UI\API;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class APIController extends TestCase
{
    public const LOCALHOST = 'http://localhost/planing_travels/public/index.php/';

    protected $client;

    protected function setUp()
    {
        $this->initializeClient();
    }

    private function initializeClient(): void
    {
        $this->client = new Client();
    }
}
