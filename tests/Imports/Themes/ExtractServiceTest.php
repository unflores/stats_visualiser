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
        $ExtractServices = new ExtractService($this->entityManager);
        $excel_file = $this->projectDir.'/var/import-data/emissions_GES_structure.xlsx';
        $themes = $ExtractServices->GetThemesFromExcelFile($excel_file);
        $preparedThemes = $ExtractServices->PrepareThemesForDatabase($themes);
        $savedThemesCount = $ExtractServices->SaveThemesOnDatabase($preparedThemes);

        $this->assertEquals(
            $savedThemesCount,
            count($preparedThemes),
            'Themes are saved'
        );

        $this->assertEquals(
            1,
            count($this->themeRepository->findBy(['parentId' => null])),
            "One import doesn't have a parentId"
        );
    }
}
