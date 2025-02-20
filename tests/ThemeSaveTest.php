<?php

namespace App\Tests\Repository;

use App\Entity\Theme;
use App\Script\SaveTheme;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ThemeSaveTest extends KernelTestCase
{
    private $entityManager;
    private $themeRepository;
    private $projectDir;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->projectDir = self::$kernel->getContainer()->getParameter('kernel.project_dir');
        $this->entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
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

    public function testSaveImportedThemes(): void
    {
        $theme = new Theme();
        $saveTheme = new SaveTheme();
        $file = $this->projectDir.'/public/File/themes.json';
        $themes = $saveTheme->saveOnDatabase($file);

        foreach ($themes as $theme) {
            $this->entityManager->persist(
                (new Theme())
                    ->setCode($theme['code'])
                    ->setId($theme['id'])
                    ->setParentId($theme['parentId'])
                    ->setExternalId($theme['externalId'])
                    ->setIsSection($theme['isSection'])
            );
        }
        $this->entityManager->flush();
        $this->assertCount(count($themes), $this->themeRepository->findAll());
    }
}
