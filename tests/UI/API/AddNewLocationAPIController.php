<?php

namespace App\Tests\UI\API;

class AddNewLocationAPIController extends APIController
{
    private $api = 'api/user/1/location';

    public function setUp()
    {
        parent::setUp();
    }

    public function testGetLocationsByTravel()
    {
        $url = self::LOCALHOST.$this->api;
        $response = $this->client->request('POST', $url, []);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
