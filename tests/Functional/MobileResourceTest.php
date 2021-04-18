<?php

namespace App\Tests\Functional;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class MobileResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    Public function testReadMobile()
    {
        $client = self::createClient();

        $client->request('GET', '/api/mobiles', [
            'headers' => ['accept' => 'application/json']
        ]);

        $this->assertResponseStatusCodeSame(401);

        $email = 'user1@example.com';
        $password = 'KnockKnock';

        $email2 = 'user2@example.com';

        $user1 = $this->createCustumer($email, $password);
        $user2 = $this->createCustumer($email2, $password);
        $this->login($client, $email, $password);

        $this->assertResponseStatusCodeSame(204);

        $client->request('GET', '/api/custumers/'.$user2->getId(), [
            'headers' => ['accept' => 'application/json']
        ]);

        $this->assertResponseStatusCodeSame(403);

        $client->request('GET', '/api/custumers/'.$user1->getId(), [
            'headers' => ['accept' => 'application/json']
        ]);

        $this->assertResponseStatusCodeSame(200);
    }
}