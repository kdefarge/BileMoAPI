<?php

namespace App\Tests\Functional;

use App\Entity\Custumer;
use App\Entity\User;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UsersResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    private $requestLink = '/api/users';

    public function testUsersReadList()
    {
        $client = self::createClient();

        $client->request('GET', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
        ]);

        // Check that a visitor cannot see mobiles
        $this->assertResponseStatusCodeSame(401);

        $userToken = $this->retrieveTokenFixtures($client, 'user1');

        $client->request('GET', $this->requestLink, [
            'headers' => ['accept' => 'application/ld+json'],
            'auth_bearer' => $userToken
        ]);

        // Check that a user can see mobiles
        $this->assertResponseStatusCodeSame(200);

        /** @var Custumer $custumer */
        $custumer = $this->getRepository(Custumer::class)
            ->findOneBy(['email' => 'user1@example.org']);

        $totalItems = (int) $this->getRepository(User::class)
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->andWhere('u.custumer = :val')
            ->setParameter('val', $custumer)
            ->getQuery()
            ->getSingleScalarResult();

        $array = ['hydra:totalItems' => $totalItems];
        $this->assertJsonContains($array);
    }

    public function testUserReadItem()
    {
        $client = self::createClient();

        /** @var Custumer $custumer */
        $custumer = $this->getRepository(Custumer::class)
            ->findOneBy(['email' => 'user1@example.org']);

        /** @var user $user */
        $user = $this->getRepository(User::class)
            ->findOneBy(['custumer' => $custumer]);

        $userLink = $this->requestLink . '/' . $user->getId();

        $client->request('GET', $userLink, [
            'headers' => ['accept' => 'application/json']
        ]);

        // Check that a visitor does not have the rights to see mobile
        $this->assertResponseStatusCodeSame(401);

        $userToken = $this->retrieveTokenFixtures($client, 'user1');

        $client->request('GET', $userLink, [
            'headers' => ['accept' => 'application/json'],
            'auth_bearer' => $userToken
        ]);

        // Check an owner can see his user
        $this->assertResponseStatusCodeSame(200);

        $user2Token = $this->retrieveTokenFixtures($client, 'user2');

        $client->request('GET', $userLink, [
            'headers' => ['accept' => 'application/json'],
            'auth_bearer' => $user2Token
        ]);

        // A non-owner cannot see the user of another
        $this->assertResponseStatusCodeSame(404);
    }

    public function testUsersWrite()
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

        // Check not owner cannot update user (404)
        $this->assertResponseStatusCodeSame(404);

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

        // Check not owner cannot delete user (404)
        $this->assertResponseStatusCodeSame(404);

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
