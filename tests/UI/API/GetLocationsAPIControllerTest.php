<?php

namespace App\Tests\UI\API;

class GetLocationsAPIControllerTest extends APIController
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testGetLocationsByTravelRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/travel/some-travel-id/locations');

        $response = $client->getResponse();
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testGetLocationsByTravelReturnsJsonStructure(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/travel/some-travel-id/locations');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }
}
