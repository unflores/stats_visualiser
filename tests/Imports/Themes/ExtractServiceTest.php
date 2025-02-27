<?php

namespace App\Tests;

use App\Entity\Theme;
use App\Imports\Themes\ExtractService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ExtractServiceTest extends KernelTestCase
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
        $ExtractServices = new ExtractService($this->entityManager, $this->projectDir);
        $saveThemes = $ExtractServices->SaveThemesOnDatabase();
        $this->assertTrue($saveThemes, 'themes are saved');
        $this->assertEquals(118, $this->themeRepository->count([]), $this->themeRepository->count([]).' theme(s) found ');
        $this->assertTrue($this->themeRepository->checkAllParentIdNotNull(), 'all parentId are not Null');
    }
}
