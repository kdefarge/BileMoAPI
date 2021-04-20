<?php
// tests/AuthenticationTest.php

namespace App\Tests;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class AuthenticationTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testLoginJWT(): void
    {
        $client = self::createClient();

        $email = 'user@example.com';
        $password = 'KnockKnock';

        $this->createCustumer($email, $password);

        // retrieve a token
        $response = $client->request('POST', '/authentication_token', [
            'json' => [
                'email' => $email,
                'password' => $password,
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // test not authorized
        $client->request('GET', '/api/mobiles', [
            'headers' => ['accept' => 'application/json']
        ]);
        $this->assertResponseStatusCodeSame(401);

        // test authorized
        $client->request('GET', '/api/mobiles', [
            'headers' => [
                'accept' => 'application/json',
            ],
            'auth_bearer' => $json['token']
        ]);
        
        $this->assertResponseIsSuccessful();
    }
}
