<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ThemesTest extends ApiTestCase
{
    public function testSomething(): void
    {
        $response = static::createClient()->request('GET', '/api/themes');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['themes' => [
            [
                'code' => 'environment',
            ],
        ]]);
    }
}
