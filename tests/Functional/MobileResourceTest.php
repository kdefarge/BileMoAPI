<?php

namespace App\Tests\Functional;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class MobileResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    private $requestLink = '/api/mobiles';

    public function testMobileReadList()
    {
        $client = self::createClient();

        $client->request('GET', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
        ]);

        // Check that a visitor cannot see mobiles
        $this->assertResponseStatusCodeSame(401);

        $userToken = $this->retrieveTokenFixtures($client, 'user1');

        $client->request('GET', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
            'auth_bearer' => $userToken
        ]);

        // Check that a user can see the list of mobiles
        $this->assertResponseStatusCodeSame(200);

        $adminToken = $this->retrieveTokenFixtures($client, 'admin');

        $client->request('GET', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
            'auth_bearer' => $adminToken
        ]);

        // Check that an admin can see the list of mobiles
        $this->assertResponseStatusCodeSame(200);
    }

    public function testMobileReadItem()
    {
        $client = self::createClient();

        $mobile = $this->createMobile('toto', 'in the room', 55, 77);

        $mobileLink = $this->requestLink . '/' . $mobile->getId();

        $client->request('GET', $mobileLink, [
            'headers' => ['accept' => 'application/json']
        ]);

        // Check that a visitor does not have the rights to see mobile
        $this->assertResponseStatusCodeSame(401);

        $userToken = $this->retrieveTokenFixtures($client, 'user1');

        $client->request('GET', $mobileLink, [
            'headers' => ['accept' => 'application/json'],
            'auth_bearer' => $userToken
        ]);

        // Check that a user can see a mobile
        $this->assertResponseStatusCodeSame(200);

        $adminToken = $this->retrieveTokenFixtures($client, 'admin');

        $client->request('GET', $mobileLink, [
            'headers' => ['accept' => 'application/json'],
            'auth_bearer' => $adminToken
        ]);

        // Check that an admin can see a mobile
        $this->assertResponseStatusCodeSame(200);

        $this->removeEntity($mobile);

        $client->request('GET', $mobileLink, [
            'headers' => ['accept' => 'application/json'],
            'auth_bearer' => $userToken
        ]);

        // Check that a mobile does not exist
        $this->assertResponseStatusCodeSame(404);
    }

    public function testMobileWrite()
    {
        $client = self::createClient();

        $json = [
            'modelName' => 'fixture model',
            'description' => 'description',
            'price' => 5200,
            'stock' => 100
        ];

        $client->request('POST', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
            'json' => $json
        ]);

        // Check that a visitor cannot create a new mobile
        $this->assertResponseStatusCodeSame(401);

        $userToken = $this->retrieveTokenFixtures($client, 'user1');

        $client->request('POST', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
            'json' => $json,
            'auth_bearer' => $userToken
        ]);

        // Check that a user cannot create a new mobile
        $this->assertResponseStatusCodeSame(403);

        $adminToken = $this->retrieveTokenFixtures($client, 'admin');

        $response = $client->request('POST', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
            'json' => $json,
            'auth_bearer' => $adminToken
        ]);

        // Check admin can create a new mobile
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($json);

        $mobileLink = $this->requestLink . '/' . $response->toArray()['id'];

        $client->request('POST', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
            'json' => $json,
            'auth_bearer' => $adminToken
        ]);

        // Check unique modelname
        $this->assertResponseStatusCodeSame(422);

        $jsonUpdate = [
            'modelName' => 'fixture model updated',
            'description' => 'description updated',
            'price' => 1337,
            'stock' => 42
        ];

        $client->request('PATCH', $mobileLink, [
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/merge-patch+json'
            ],
            'json' => $jsonUpdate,
            'auth_bearer' => $adminToken
        ]);

        // Check admin update mobile
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains($jsonUpdate);

        $client->request('DELETE', $mobileLink, [
            'headers' => [
                'accept' => 'application/json',
            ],
            'auth_bearer' => $adminToken
        ]);

        // Check admin delete mobile
        $this->assertResponseStatusCodeSame(204);
    }
}
