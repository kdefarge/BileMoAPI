<?php

namespace App\Tests\Functional;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class MobileResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testReadMobile()
    {
        $client = self::createClient();

        $email = 'user@example.com';
        $password = 'KnockKnock';

        $this->createCustumer($email, $password);

        $token = $this->retrieveToken($client, $email, $password);

        $client->request('GET', '/api/mobiles', [
            'headers' => ['accept' => 'application/json'],
            'auth_bearer' => $token
        ]);

        $this->assertResponseIsSuccessful();

        $mobile = $this->createMobile('toto', 'in the room', 55, 77);

        $client->request('GET', '/api/mobiles/'.$mobile->getId(), [
            'headers' => ['accept' => 'application/json']
        ]);

        $this->assertResponseStatusCodeSame(401);

        $client->request('GET', '/api/mobiles/'.$mobile->getId(), [
            'headers' => ['accept' => 'application/json'],
            'auth_bearer' => $token
        ]);

        $this->assertResponseStatusCodeSame(200);
        
        $this->removeEntity($mobile);
        
        $client->request('GET', '/api/mobiles/'.$mobile->getId(), [
            'headers' => ['accept' => 'application/json'],
            'auth_bearer' => $token
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testWriteMobile()
    {
        
    }
}
