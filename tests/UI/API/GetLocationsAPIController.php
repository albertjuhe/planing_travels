<?php

namespace App\Tests\UI\API;

class GetLocationsAPIController extends APIController
{
    const API_GET_LOCATION = 'api/travel/2/locations';

    public function setUp()
    {
        parent::setUp();
    }

    public function testGetLocationsByTravel()
    {
        $url = self::LOCALHOST.self::API_GET_LOCATION;
        $response = $this->client->request('GET', $url, []);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
