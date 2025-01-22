<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ThemesTest extends WebTestCase
{
  // Ideally this should be replaced with a more comprehensive test
  // once we have our data model in place
    public function testApiResponse(): void
    {

        $client = static::createClient();
        $client->request('GET', '/api/themes');

        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();

        $results = json_decode($content, true);
        $this->assertEquals('environment', $results['themes'][0]['code']);

    }
}
