<?php

namespace App\Import;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class IngestTheme
{
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

        $newSpreadsheet = new Spreadsheet();
        $newSheet = $newSpreadsheet->getActiveSheet();

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

    /**
     * replace the underscore
     * to space between the word or word group.
     */
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

    // cette function n'est pas appellé
    /*private static function getCategorieIdByCategorie(array $themes, string $categorie): string
    {
        foreach ($themes as $theme) {
            if ($theme['categories'] === $categorie) {
                return $theme['categories_id'];
            }
        }
        return '';
    }*/

    private function getCodeConcatenateByCategorieId(array $themes, string $level_id): string
    {
        $levels = [];
        $hierarchie = [];
        $levels = $this::gethierarchieLevels($level_id);
        foreach ($levels as $level) {
            $hierarchie[] = $this::getCategoriesByCategorieId($themes, $level);
        }

        return implode('.', $hierarchie);
    }

    private function getParentIdByChildId(string $ChildId): mixed
    {
        $check_dot = strpos($ChildId, '.');
        if (false !== $check_dot) {
            $level_array = explode('.', $ChildId);
            while (!empty($level_array)) {
                $hierarchie[] = implode('.', $level_array);
                array_pop($level_array);
            }
            $level_array = array_reverse($hierarchie);

            return $level_array[count($level_array) - 2];
        } else {
            return 'null';
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

    public function PrepareThemesForDatabase(array $themes): mixed
    {
        $categories_id = $this->getCategoriesId($themes);
        $themes_json = [];
        $y = 1;
        for ($i = 0; $i < count($categories_id); ++$i, ++$y) {
            $themes_json[] = [
                'code' => $this->getCodeConcatenateByCategorieId($themes, $categories_id[$i]),
                'externalId' => $categories_id[$i],
                'isSection' => true,
                'parentId' => $this->getParentIdByChildId($categories_id[$i]),
            ];
        }

        return json_encode($themes_json);
    }
}
