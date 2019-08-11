<?php

namespace App\Tests\UI\API;

use GuzzleHttp\Client;

class DeleteLocationAPIControllerTest extends APIControllerTest
{
    private $api = '/api/travel/2/location/1';

    public function setUp()
    {
        parent::setUp();
    }

    public function testGetLocationsByTravel()
    {
        $client = new Client();
        $url = $this->endPoint.$this->api;
        $response = $client->request('DELETE', $url, []);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
