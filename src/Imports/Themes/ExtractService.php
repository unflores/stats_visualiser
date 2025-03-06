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
            $cellA = $sheet->getCell('A'.$rowIndex)->getValue();
            $cellB = $sheet->getCell('B'.$rowIndex)->getValue();
            if (null !== $cellA && null !== $cellB) {
                if ($rowIndex > 2) {
                    $themes[] = [
                        'categories_id' => $cellB,
                        'categories' => $this->replaceSpaceByUnderscore($cellA),
                    ];
                }
            }
        }

        return $themes;
    }

    private function replaceSpaceByUnderscore(string $word): string
    {
        $word = str_replace('/', 'et ', $word);
        $word = str_replace(' ', '_', $word);

        return $word;
    }

    private static function getHierarchieLevels(string $level): mixed
    {
        $hierarchie = [];
        $check_dot = strpos($level, '.');

        if (false !== $check_dot) {
            $level_array = explode('.', $level);
            while (!empty($level_array)) {
                $hierarchie[] = implode('.', $level_array);
                array_pop($level_array);
            }

            return array_reverse($hierarchie);
        } else {
            return [$level];
        }
    }

    private static function getCategoriesByCategorieId(array $themes, string $categorie_id): string
    {
        foreach ($themes as $theme) {
            if ($theme['categories_id'] === $categorie_id) {
                return $theme['categories'];
            }
        }

        return '';
    }

    private function getNameConcatenateByCategorieId(array $themes, string $level_id): string
    {
        $levels = [];
        $hierarchie = [];
        $levels = $this::gethierarchieLevels($level_id);
        foreach ($levels as $level) {
            $hierarchie[] = $this::getCategoriesByCategorieId($themes, $level);
        }

        return implode('.', $hierarchie);
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

    private function getCategoriesId($themes): array
    {
        $categories_id = [];
        foreach ($themes as $theme) {
            $categories_id[] = $theme['categories_id'];
        }

        return $categories_id;
    }

    public function PrepareThemesForDatabase(array $themes): array
    {
        $categories_id = $this->getCategoriesId($themes);
        $themes_array = [];
        $size = count($categories_id);

        for ($i = 0; $i < $size; ++$i) {
            $themes_array[] = [
                'id' => $i + 1,
                'name' => $this->getNameConcatenateByCategorieId($themes, $categories_id[$i]),
                'externalId' => $categories_id[$i],
                'isSection' => true,
                'parentExternalId' => $this->getParentExternalId($categories_id[$i]),
            ];
        }

        return $themes_array;
    }

    public function SaveThemesOnDatabase(array $arrayThemes): bool
    {
        $savedThemes = false;

        if (empty($arrayThemes)) {
            return false;
        }

        foreach ($arrayThemes as $theme) {
            if (0 === $this->themeRepository->count([])) {
                $newTheme = (new Theme())
                ->setName($theme['name'])
                ->setExternalId($theme['externalId'])
                ->setIsSection($theme['isSection'])
                ->setParentId(null);
                $this->entityManager->persist($newTheme);
                $this->entityManager->flush();
                $savedThemes = true;
            } else {
                $newTheme = (new Theme())
                    ->setName($theme['name'])
                    ->setExternalId($theme['externalId'])
                    ->setIsSection($theme['isSection'])
                    ->setParentId($this->themeRepository->getParentIdByparentExternalId($theme['parentExternalId']));
                $this->entityManager->persist($newTheme);
                $this->entityManager->flush();
                $savedThemes = true;
            }
        }

        return $savedThemes;
    }
}
