<?php

namespace App\Tests;

use App\Entity\Theme;
use App\Imports\Themes\ExtractService;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ExtractServiceTest extends KernelTestCase
{
    private $entityManager;
    private $themeRepository;
    private $projectDir;
    private  $worksheet;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->themeRepository = $this->entityManager->getRepository(Theme::class);
        $this->projectDir = $container->getParameter('kernel.project_dir');
        $this->worksheet = $this->createMock(Worksheet::class);
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
        $ExtractServices = new ExtractService(entityManager: $this->entityManager, worksheet: $this->worksheet);
        // TODO: change this to inject the spreadsheet so that we can test the integration more easily.
        $excel_file = $this->projectDir.'/tests/Imports/Themes/test-themes.xlsx';
        $themes = $ExtractServices->GetThemesFromExcelFile($excel_file);
        $preparedThemes = $ExtractServices->PrepareThemesForDatabase($themes);
        $savedThemesCount = $ExtractServices->SaveThemesOnDatabase($preparedThemes);

        $this->assertEquals(
            $savedThemesCount,
            count($preparedThemes),
            'Themes are saved'
        );

        $imported_theme = $this->themeRepository->findOneBy(['externalId' => 'V0.1']);

        $this->assertEquals(
            $imported_theme->getName(), 'par gaz à effet de serre'
        );

        $this->assertEquals(
            1,
            count($this->themeRepository->findBy(['parentId' => null])),
            "One import doesn't have a parentId"
        );
    }
}
