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
            'auth' => ['ajuhe', 'e134le41'],
        ]
        );
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPOSTInvalidToken(): void
    {
        $response = $this->client->request(
            'POST',
            'http://travelexperience.com/api/tokens?XDEBUG_SESSION_START=11940',
            [
                'auth' => ['ajuhe', 'e134le41t'],
            ]
        );
        $this->assertEquals(401, $response->getStatusCode());
    }
}
