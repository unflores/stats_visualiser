<?php

namespace App\Tests\Repository;

use App\Entity\Theme;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ThemeRepositoryTest extends KernelTestCase
{

    public function testAddParentTheme(): void
    {
       self::bootKernel();
       $container = static::getContainer();

        $entityManager = $container->get('doctrine.orm.entity_manager');
        $theme = new Theme();
        $theme->setCode('environnement');
        $theme->setIsSection(true);
        $theme->setParentId(null);
        $theme->setExternalId('2980');
        $entityManager->persist($theme);
        $entityManager->flush();
        
        $themeRepository = $entityManager->getRepository(Theme::class);
        $savedTheme = $themeRepository->find($theme->getId());

        $this->assertNotNull($savedTheme);
        $this->assertSame('environnement', $savedTheme->getCode());

    }

    public function testAddChildTheme():void
    {
        self::bootKernel();
       $container = static::getContainer();

        $entityManager = $container->get('doctrine.orm.entity_manager');
        $theme = new Theme();
        $theme->setCode('Emissions GES');
        $theme->setIsSection(true);
        $theme->setParentId(1);
        $theme->setExternalId('2981');
        $entityManager->persist($theme);
        $entityManager->flush();
        
        $themeRepository = $entityManager->getRepository(Theme::class);
        $savedTheme = $themeRepository->find($theme->getId());

        $this->assertNotNull($savedTheme);
        $this->assertSame(1, $savedTheme->getParentId());
        $this->assertSame("Emissions GES", $savedTheme->getCode());
    }
}
