<?php

namespace App\Controller\Extra;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ExcelService;

class ExcelController extends AbstractController 
{

    #[Route('/extracts', name:'extracts')]
    public function extractColumn(ExcelService $excelService): Response
    {
        $file = '/public/File/Z_CITEPA_emissions_GES_structure_.xlsx';
        $filePath = $this->getParameter('kernel.project_dir') .$file;
        $columnLetter = 'F';
        $data = $excelService->extractColumnFromExcel($filePath, $columnLetter);
        return $this->json($data);
    }
}