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

        $client->request('GET', '/api/mobiles', [
            'headers' => ['accept' => 'application/json']
        ]);

        $this->assertResponseStatusCodeSame(401);

        $this->createCustumerAndLogin($client, 'custumerMobile@example.com', 'knockknock');

        $client->request('GET', '/api/mobiles', [
            'headers' => ['accept' => 'application/json']
        ]);

        $this->assertResponseStatusCodeSame(200);
    }
}
