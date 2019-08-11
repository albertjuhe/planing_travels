<?php

namespace App\Tests\UI\API;

use GuzzleHttp\Client;

class GetLocationsAPIControllerTest extends APIControllerTest
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testGetLocationsByTravel()
    {
        $client = new Client();

        $response = $client->request('GET', $this->endPoint.'/api/travel/2/locations', []);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
