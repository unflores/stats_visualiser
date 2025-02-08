<?php

namespace App\Tests\Repository;

use App\Entity\Theme;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ThemeRepositoryTest extends KernelTestCase
{

    private $entityManager;
    private $themeRepository;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->themeRepository = $this->entityManager->getRepository(Theme::class);
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

    public function testAddParentTheme(): void
    {
        $theme = new Theme();
        $theme->setCode('environnement');
        $theme->setIsSection(true);
        $theme->setParentId(null);
        $theme->setExternalId('1024');
        $this->entityManager->persist($theme);
        $this->entityManager->flush();

        $savedTheme = $this->themeRepository->find($theme->getId());

        $this->assertNotNull($savedTheme);
        $this->assertSame('environnement', $savedTheme->getCode());
    }

    public function testAddChildTheme():void
    {
        $child = new Theme();
        $child->setCode('Emissions GES');
        $child->setIsSection(true);
        $child->setParentId(1);
        $child->setExternalId('2981');

        $this->entityManager->persist($child);
        $this->entityManager->flush();

        $savedTheme = $this->themeRepository->find($child->getId());

        $this->assertNotNull($savedTheme);
        $this->assertSame(1, $savedTheme->getParentId());
        $this->assertSame('Emissions GES', $savedTheme->getCode());
        $this->assertSame(true, $savedTheme->getIsSection());
        $this->assertSame('2981', $savedTheme->getExternalId());
    }
}
