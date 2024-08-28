<?php

namespace App\Tests\UI\API;

class GetLocationsAPIController extends APIController
{
    public const API_GET_LOCATION = 'api/travel/9c7299d3-665b-4469-ba47-9020c38e91d7/locations';

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
