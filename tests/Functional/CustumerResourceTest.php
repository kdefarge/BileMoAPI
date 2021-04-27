<?php

namespace App\Tests\Functional;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use App\Entity\Custumer;

class CustumerResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    private $requestLink = '/api/custumers';

    public function testCustumerReadList()
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

        // Check that a user cannot see mobiles
        $this->assertResponseStatusCodeSame(403);

        $adminToken = $this->retrieveTokenFixtures($client, 'admin');

        $client->request('GET', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
            'auth_bearer' => $adminToken
        ]);

        // Check that an admin can see the list of mobiles
        $this->assertResponseStatusCodeSame(200);
    }

    public function testCustumerReadItem()
    {
        $client = self::createClient();

        $repository = $this->getRepository(Custumer::class);

        /** @var Custumer $custumer */
        $custumer = $repository->findOneBy(['email' => 'user2@example.org']);

        $custumerLink = $this->requestLink . '/' . $custumer->getId();

        $client->request('GET', $custumerLink, [
            'headers' => ['accept' => 'application/json']
        ]);

        // Check that a visitor does not have the rights to see custumer
        $this->assertResponseStatusCodeSame(401);

        $userToken = $this->retrieveTokenFixtures($client, 'user1');

        $client->request('GET', $custumerLink, [
            'headers' => ['accept' => 'application/json'],
            'auth_bearer' => $userToken
        ]);

        // Check that a user cannot see custumer
        $this->assertResponseStatusCodeSame(403);

        $adminToken = $this->retrieveTokenFixtures($client, 'admin');

        $client->request('GET', $custumerLink, [
            'headers' => ['accept' => 'application/json'],
            'auth_bearer' => $adminToken
        ]);

        // Check that an admin can see a custumer
        $this->assertResponseStatusCodeSame(200);

        $this->removeEntity($custumer);

        $client->request('GET', $custumerLink, [
            'headers' => ['accept' => 'application/json'],
            'auth_bearer' => $userToken
        ]);

        // Check that a mobile does not exist
        $this->assertResponseStatusCodeSame(404);
    }

    public function testCustumerWrite()
    {
        $client = self::createClient();

        $json = [
            'email' => 'userFixture@example.org',
            'password' => 'toctoc',
            'name' => 'userF',
            'fullname' => 'User Fixture',
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
        unset($json['password']);
        $this->assertJsonContains($json);
        
        $json['password'] = 'toctoc';
        $mobileLink = $this->requestLink . '/' . $response->toArray()['id'];

        $client->request('POST', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
            'json' => $json,
            'auth_bearer' => $adminToken
        ]);

        // Check unique email
        $this->assertResponseStatusCodeSame(422);

        $jsonUpdate = [
            'email' => 'userFixtureUpdated@example.org',
            'password' => 'toctoc',
            'name' => 'userFUpdated',
            'fullname' => 'User Updated',
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
        unset($jsonUpdate['password']);
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
