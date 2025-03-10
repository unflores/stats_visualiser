<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Theme;
class ThemesTest extends WebTestCase
{

    private $client;
    private $themeRepository;
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();


        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->themeRepository = $this->entityManager->getRepository(Theme::class);

        $parent = new Theme();
        $parent->setName('Environment');
        $parent->setIsSection(true);
        $parent->setParentId(null);
        $parent->setExternalId('external_id1');
        $this->entityManager->persist($parent);
        $this->entityManager->flush();
    }

    protected function tearDown(): void
    {
        $themes = $this->themeRepository->findAll();
        foreach ($themes as $theme) {
            $this->entityManager->remove($theme);
        }
        $this->entityManager->flush();
        parent::tearDown();
    }

    public function testApiResponse(): void
    {
        $this->client->request('GET', '/api/themes');

        $this->assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();

        $results = json_decode($content, true);
        $this->assertEquals('Environment', $results['base'][0]['name']);
    }
}
