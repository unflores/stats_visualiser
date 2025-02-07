<?php

namespace App\Controller\Extra;

use App\Service\ExcelService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExcelController extends AbstractController
{
    #[Route('/extracts', name: 'extract_column', methods: ['GET'])]
    public function extractColumn(ExcelService $excelService): JsonResponse
    {
        $filePath = $this->getParameter('kernel.project_dir').'/public/File/Z_CITEPA_emissions_GES_structure_.xlsx';
        $columnLetter = 'A';

        $data = $excelService->extractColumnFromExcel($filePath, $columnLetter);

        if (isset($data['error'])) {
            return new JsonResponse($data, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(
            $data, Response::HTTP_OK, []);
    }
}
