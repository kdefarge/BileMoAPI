<?php

namespace App\Tests\Functional;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class TokenTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testToken()
    {
        $client = self::createClient();

        $response = $client->request('POST', '/api/tokens', [
            'json' => [
                'username' => 'johndoe',
                'password' => 'test'
            ],
        ]);

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('token', $data);
    }
}
