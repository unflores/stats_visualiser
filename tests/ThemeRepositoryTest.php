<?php

namespace App\Tests\Repository;

use App\Entity\Theme;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ThemeRepositoryTest extends KernelTestCase
{
    public function testAddThemeToDatabase(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $entityManager = $container->get('doctrine.orm.entity_manager');

        $theme = new Theme();
        $theme->setParentId(20);

        $entityManager->persist($theme);
        $entityManager->flush();

        $themeRepository = $entityManager->getRepository(Theme::class);
        $savedTheme = $themeRepository->find($theme->getId());

        $this->assertNotNull($savedTheme);
        $this->assertSame(20, $savedTheme->getParentId());
    }
}
