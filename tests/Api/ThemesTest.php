<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ThemesTest extends ApiTestCase
{
  // Ideally this should be replaced with a more comprehensive test
  // once we have our data model in place
    public function testApiResponse(): void
    {

        echo "APP_ENV: " . $_SERVER['APP_ENV'] . "\n";
        $response = static::createClient()->request('GET', '/api/themes');
        echo "hai bro";
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['themes' => [
            [
                'code' => 'environment',
            ],
        ]]);
    }
}
