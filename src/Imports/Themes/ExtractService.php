<?php

namespace App\Imports\Themes;

use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExtractService
{
    private $entityManager;
    private $themeRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->themeRepository = $entityManager->getRepository(Theme::class);
    }

    /**
     * Get Themes From Excel File.
     *
     * @return array themes import from the excel file
     */
    public function GetThemesFromExcelFile(string $excel_file): array
    {
        $themes = [];
        if (!file_exists($excel_file)) {
            return ['Excel File not found'];
        }
        $spreadsheet = IOFactory::load($excel_file);
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getRowIterator() as $row) {
            $rowIndex = $row->getRowIndex();
            $name = $sheet->getCell('A'.$rowIndex)->getValue();
            $external_id = $sheet->getCell('B'.$rowIndex)->getValue();
            if (null !== $name && null !== $external_id) {
                if ($rowIndex > 2) {
                    $themes[] = [
                        'categories_id' => $external_id,
                        'categories' => $name,
                    ];
                }
            }
        }

        return $themes;
    }

    private function getParentExternalId(string $externalId): mixed
    {
        $check_dot = strpos($externalId, '.');
        if (false !== $check_dot) {
            $level_array = explode('.', $externalId);
            array_pop($level_array);

            return implode('.', $level_array);
        } else {
            return null;
        }
    }

    public function PrepareThemesForDatabase(array $themes): array
    {
        return array_map(function($theme) {
            return [
                'name' => $theme['categories'],
                'externalId' => $theme['categories_id'],
                'isSection' => true,
                'parentExternalId' => $this->getParentExternalId($theme['categories_id']),
            ];
        }, $themes);
    }

    public function SaveThemesOnDatabase(array $arrayThemes): bool
    {
        $savedThemes = false;

        if (empty($arrayThemes)) {
            return false;
        }

        foreach ($arrayThemes as $theme) {
            $newTheme = (new Theme())
                ->setName($theme['name'])
                ->setExternalId($theme['externalId'])
                ->setIsSection($theme['isSection'])
                ->setParentId($this->themeRepository->getParentIdByparentExternalId($theme['parentExternalId']));
            $this->entityManager->persist($newTheme);
            $this->entityManager->flush();
            $savedThemes = true;

        }

        return $savedThemes;
    }
}
