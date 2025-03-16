<?php

namespace App\Imports\Themes;

use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class ExtractService
{
    private $entityManager;
    private $themeRepository;
    private $worksheet;

    public function __construct(EntityManagerInterface $entityManager, Worksheet $worksheet)
    {
        $this->entityManager = $entityManager;
        $this->themeRepository = $entityManager->getRepository(Theme::class);
        $this->worksheet = $worksheet;
    }

    /**
     * Get Themes From Excel File.
     *
     * @return array themes import from the excel file
     */
    public function GetThemesFromExcelFile(string $pathSheet): array
    {
        $themes = [];

        if ('xlsx' !== pathinfo(basename($pathSheet), PATHINFO_EXTENSION)) {
            throw new \Exception('The file must be an Excel file');
        }
        if (!file_exists($pathSheet)) {
            throw new FileNotFoundException(sprintf('Excel file "%s" not found', $pathSheet));
        }

        $spreadsheet = IOFactory::load($pathSheet);
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getRowIterator() as $row) {
            $rowIndex = $row->getRowIndex();
            $name = $sheet->getCell('A'.$rowIndex)->getValue();
            $external_id = $sheet->getCell('B'.$rowIndex)->getValue();
            if (null !== $name && null !== $external_id) {
                if ($rowIndex > 2) {
                    $themes[] = [
                        'externalId' => $external_id,
                        'name' => $name,
                    ];
                }
            }
        }

        return $themes;
    }

    private function getParentExternalId(string $externalId): ?string
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
        return array_map(function ($theme) {
            return [
                'name' => $theme['name'],
                'externalId' => $theme['externalId'],
                'isSection' => true,
                'parentExternalId' => $this->getParentExternalId($theme['externalId']),
            ];
        }, $themes);
    }

    public function SaveThemesOnDatabase(array $arrayThemes): int
    {
        foreach ($arrayThemes as $theme) {
            $existing_theme = $this->themeRepository->findOneBy(['externalId' => $theme['externalId']]);
            $theme_to_write = $existing_theme ?? (new Theme())->setExternalId($theme['externalId']);
            $external_id = $theme_to_write->getExternalId();
            $parent_external_id = $this->getParentExternalId($external_id);
            $parent_theme = $this->themeRepository->findOneBy(['externalId' => $parent_external_id]);

            $theme_to_write
                ->setName($theme['name'])
                ->setIsSection($theme['isSection'])
                ->setParentId($parent_theme ? $parent_theme->getId() : null);

            $this->entityManager->persist($theme_to_write);
            $this->entityManager->flush();
        }

        return count($arrayThemes);
    }
}
