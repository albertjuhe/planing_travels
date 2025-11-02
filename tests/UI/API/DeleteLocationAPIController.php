<?php

namespace App\Tests\UI\API;

class DeleteLocationAPIController extends APIController
{
    private $api = '/api/travel/2/location/1';

    public function setUp()
    {
        parent::setUp();
    }

    public function testGetLocationsByTravel()
    {
        $url = self::LOCALHOST.$this->api;
        $response = $this->client->request('DELETE', $url, []);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
