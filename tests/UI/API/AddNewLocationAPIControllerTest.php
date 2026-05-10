<?php

namespace App\Tests\UI\API;

class AddNewLocationAPIControllerTest extends APIController
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testAddLocationRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/user/1/location',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'travel' => 'some-travel-id',
                'latitude' => 41.38,
                'longitude' => 2.17,
                'place_id' => 'place123',
                'address' => 'Barcelona',
                'IdType' => 1,
                'comment' => '',
                'link' => '',
                'placeAddress' => 'Barcelona',
            ])
        );

        $response = $client->getResponse();
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }
}
