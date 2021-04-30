<?php

namespace App\Tests\Functional;

use App\Entity\Command;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use App\Entity\Custumer;
use App\Entity\Mobile;
use App\Entity\User;

class CommandsResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    private $requestLink = '/api/commands';

    public function testCommandsReadList()
    {
        $client = self::createClient();

        $client->request('GET', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
        ]);

        // Check that a visitor cannot see commands
        $this->assertResponseStatusCodeSame(401);

        $userToken = $this->retrieveTokenFixtures($client, 'user1');

        $client->request('GET', $this->requestLink, [
            'headers' => ['accept' => 'application/ld+json'],
            'auth_bearer' => $userToken
        ]);

        // An owner can only see his orders
        $this->assertResponseStatusCodeSame(200);

        /** @var Custumer $custumer */
        $custumer = $this->getRepository(Custumer::class)
            ->findOneBy(['email' => 'user1@example.org']);

        $totalItems = (int) $this->getRepository(Command::class)
            ->createQueryBuilder('c')
            ->select('count(c.id)')
            ->join('c.user', 'u')
            ->andWhere('u.custumer = :val')
            ->setParameter('val', $custumer)
            ->getQuery()
            ->getSingleScalarResult();

        $array = ['hydra:totalItems' => $totalItems];
        $this->assertJsonContains($array);

        $adminToken = $this->retrieveTokenFixtures($client, 'admin');

        $client->request('GET', $this->requestLink, [
            'headers' => ['accept' => 'application/ld+json'],
            'auth_bearer' => $adminToken
        ]);

        // An admin can see all orders
        $this->assertResponseStatusCodeSame(200);

        /** @var Custumer $custumer */
        $custumer = $this->getRepository(Custumer::class)
            ->findOneBy(['email' => 'admin@example.org']);

        $totalItems = (int) $this->getRepository(Command::class)
            ->createQueryBuilder('c')
            ->select('count(c.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $array = ['hydra:totalItems' => $totalItems];
        $this->assertJsonContains($array);
    }

    public function testCommandWrite()
    {
        $client = self::createClient();

        /** @var Mobile $mobile */
        $mobile = $this->getRepository(Mobile::class)
            ->findOneBy([]);

        /** @var Custumer $custumer */
        $custumer = $this->getRepository(Custumer::class)
            ->findOneBy(['email' => 'user1@example.org']);

        /** @var User $user */
        $user = $this->getRepository(User::class)
            ->findOneBy(['custumer' => $custumer]);

        $json = [
            'user' => '/api/users/' . $user->getId(),
            'mobiles' => [
                '/api/mobiles/' . $mobile->getId()
            ]
        ];

        $client->request('POST', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
            'json' => $json
        ]);

        // Check that a visitor cannot create a command
        $this->assertResponseStatusCodeSame(401);

        $user1Token = $this->retrieveTokenFixtures($client, 'user1');

        $response = $client->request('POST', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
            'json' => $json,
            'auth_bearer' => $user1Token
        ]);

        // Check owner can create new command
        $this->assertResponseStatusCodeSame(201);

        $commandLink = $this->requestLink . '/' . $response->toArray()['id'];

        $user2Token = $this->retrieveTokenFixtures($client, 'user2');

        $response = $client->request('POST', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
            'json' => $json,
            'auth_bearer' => $user2Token
        ]);

        // Check not owner user can't command 
        $this->assertResponseStatusCodeSame(400);

        $user1Token = $this->retrieveTokenFixtures($client, 'user1');

        $response = $client->request('POST', $this->requestLink, [
            'headers' => ['accept' => 'application/json'],
            'json' => $json,
            'auth_bearer' => $user1Token
        ]);

        // Check owner can create same command
        $this->assertResponseStatusCodeSame(201);

        $adminToken = $this->retrieveTokenFixtures($client, 'admin');

        $jsonUpdate = [
            'status' => 'Validé'
        ];

        $client->request('PATCH', $commandLink, [
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/merge-patch+json'
            ],
            'json' => $jsonUpdate,
            'auth_bearer' => $adminToken
        ]);

        // Check owner update user
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains($jsonUpdate);
    }
}
