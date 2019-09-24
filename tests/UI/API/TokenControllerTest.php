<?php

namespace App\Tests\UI\API;

use App\Domain\Common\Model\ApiCodes;
use GuzzleHttp\Exception\ClientException;

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
        try {
            $response = $this->client->request(
                'POST',
                'http://travelexperience.com/api/tokens',
                [
                    'auth' => ['ajuhe', 'xxx'],
                ]
            );
        } catch (ClientException $e) {
            $this->assertEquals(
                ApiCodes::UNAUTHORIZED,
                $e->getCode()
            );
        }
    }

    public function testPOSTInvalidUserToken(): void
    {
        try {
            $response = $this->client->request(
                'POST',
                'http://travelexperience.com/api/tokens?XDEBUG_SESSION_START=11940',
                [
                    'auth' => ['xxx', 'xxx'],
                ]
            );
        } catch (ClientException $e) {
            $this->assertEquals(
                ApiCodes::BAD_REQUEST,
                $e->getCode()
            );
        }
    }
}
