<?php

namespace App\Tests\UI\API;

use GuzzleHttp\Client;

class AddNewLocationAPIControllerTest extends APIControllerTest
{
    private $api = '/api/user/1/location';

    public function setUp()
    {
        parent::setUp();
    }

    public function testGetLocationsByTravel()
    {
        $client = new Client();
        $url = $this->endPoint.$this->api;
        $response = $client->request('POST', $url, []);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
