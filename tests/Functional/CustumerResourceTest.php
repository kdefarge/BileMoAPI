<?php

namespace App\Tests\Functional;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class CustumerResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testLogin()
    {
        $client = self::createClient();

        $email1 = 'user1@example.com';
        $email2 = 'user2@example.com';
        $password = 'KnockKnock';

        $user2 = $this->createCustumer($email2, $password);

        $this->login($client, $email2, 'Knock');

        $this->assertResponseStatusCodeSame(401);

        $user1 = $this->createCustumerAndLogin($client, $email1, $password);

        $this->assertResponseStatusCodeSame(204);

        $client->request('GET', '/api/custumers/' . $user2->getId(), [
            'headers' => ['accept' => 'application/json']
        ]);

        $this->assertResponseStatusCodeSame(403);

        $client->request('GET', '/api/custumers/' . $user1->getId(), [
            'headers' => ['accept' => 'application/json']
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testAdminLogin()
    {
        $client = self::createClient();

        $email3 = 'admin@example.com';
        $email4 = 'user4@example.com';
        $password = 'KnockKnock';

        $user3 = $this->createAdmin($email3, $password);
        $user4 = $this->createCustumer($email4, $password);

        $this->login($client, $email3, $password);

        $this->assertResponseStatusCodeSame(204);

        $client->request('GET', '/api/custumers/' . $user4->getId(), [
            'headers' => ['accept' => 'application/json']
        ]);

        $this->assertResponseStatusCodeSame(200);
    }
}
