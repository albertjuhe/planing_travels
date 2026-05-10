<?php

namespace App\Tests\UI\API;

class DeleteLocationAPIControllerTest extends APIController
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testDeleteLocationRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/travel/some-travel-id/location/some-location-id');

        $response = $client->getResponse();
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }
}
