<?php

namespace App\Tests\Functional;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UsersResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    private $requestLink = '/api/users';

    public function testUserWrite()
    {
        $client = self::createClient();

        $json = [
            'email' => 'user_@example.org',
            'firstname' => 'testFirst',
            'lastname' => 'testLast',
        ];

        $client->request('POST', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
            'json' => $json
        ]);

        // Check that a visitor cannot create a new user
        $this->assertResponseStatusCodeSame(401);

        $userToken = $this->retrieveTokenFixtures($client, 'user1');

        $response = $client->request('POST', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
            'json' => $json,
            'auth_bearer' => $userToken
        ]);

        // Check owner can create new user
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($json);

        $mobileLink = $this->requestLink . '/' . $response->toArray()['id'];

        $client->request('POST', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
            'json' => $json,
            'auth_bearer' => $userToken
        ]);

        // Check unique modelname
        $this->assertResponseStatusCodeSame(422);

        $jsonUpdate = [
            'email' => 'user_update@example.org',
            'firstname' => 'testFirst Update',
            'lastname' => 'testLast Update',
        ];

        $user2Token = $this->retrieveTokenFixtures($client, 'user2');

        $client->request('PATCH', $mobileLink, [
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/merge-patch+json'
            ],
            'json' => $jsonUpdate,
            'auth_bearer' => $user2Token
        ]);

        // Check not owner cannot update user
        $this->assertResponseStatusCodeSame(403);

        $client->request('PATCH', $mobileLink, [
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/merge-patch+json'
            ],
            'json' => $jsonUpdate,
            'auth_bearer' => $userToken
        ]);

        // Check owner update user
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains($jsonUpdate);

        $client->request('DELETE', $mobileLink, [
            'headers' => [
                'accept' => 'application/json',
            ],
            'auth_bearer' => $user2Token
        ]);

        // Check not owner cannot delete user
        $this->assertResponseStatusCodeSame(403);

        $client->request('DELETE', $mobileLink, [
            'headers' => [
                'accept' => 'application/json',
            ],
            'auth_bearer' => $userToken
        ]);

        // Check owner delete user
        $this->assertResponseStatusCodeSame(204);
    }
}
