<?php

namespace App\Tests\UI\API;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class APIController extends TestCase
{
    const LOCALHOST = 'http://travelexperience.com/';

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
