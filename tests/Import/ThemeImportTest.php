<?php

namespace App\Tests;

use App\Entity\Theme;
use App\Import\IngestTheme;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ThemeImportTest extends KernelTestCase
{
    private $entityManager;
    private $themeRepository;
    private $projectDir;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->themeRepository = $this->entityManager->getRepository(Theme::class);
        $this->projectDir = $container->getParameter('kernel.project_dir');
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

    public function testImportThemeSave(): void
    {
        $ingestthemes = new IngestTheme($this->entityManager, $this->projectDir);
        $check_table = $ingestthemes->SaveThemesOnDatabase();
        if ($check_table) {
            $themes_table = $this->themeRepository->findAll();
            $this->assertNotEmpty($themes_table);
        }
    }
}
