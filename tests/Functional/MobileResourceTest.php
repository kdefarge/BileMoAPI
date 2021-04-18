<?php

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class MobileResourceTest extends ApiTestCase
{
    Public function testReadMobile()
    {
        $this->assertEquals(42,42);
    }
}