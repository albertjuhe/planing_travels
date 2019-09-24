<?php

namespace App\Tests\UI\API;

class TokenControllerTest extends APIController
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testPOSTCreateToken(): void
    {
        $response = $this->client->request(
            'POST',
            'http://travelexperience.com/api/tokens',
            [
            'auth' => 'aaa', 'ppp',
        ]
        );
        $this->assertEquals(200, $response->getStatusCode());
    }
}
