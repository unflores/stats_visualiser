<?php

namespace App\Controller\Api;

use App\Import\IngestTheme;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class ThemesController extends AbstractController
{
    #[Route('/themes', name: 'app_themes', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'themes' => [
                [
                    'code' => 'environment',
                    'id' => 1234,
                    'parentId' => null,
                    'children' => [
                        [
                            'code' => 'some_sub_theme',
                            'id' => 1024,
                            'parentId' => 1234,
                            'children' => [
                                [
                                    'code' => 'yet_another_sub_theme',
                                    'id' => 2048,
                                    'parentId' => 1024,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    #[Route('/show', name: 'app_themes', methods: ['GET'])]
    public function ShowThemeJson(): JsonResponse
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $excel_file = $projectDir.'/public/File/emissions_GES_structure.xlsx';
        $ingestTheme = new IngestTheme();
        $array_themes = $ingestTheme->PrepareThemesForDatabase($ingestTheme->GetThemesFromExcelFile($excel_file));

        return $this->json(json_decode($array_themes), 200);
    }
}
