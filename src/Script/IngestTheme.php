<?php

namespace App\Script;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class IngestTheme
{
    public function GetJsonDataFileXlsx(string $file): mixed
    {
        $data = [];

        if (!file_exists($file)) {
            return ['File not found'];
        }
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();

        $newSpreadsheet = new Spreadsheet();
        $newSheet = $newSpreadsheet->getActiveSheet();

        foreach ($sheet->getRowIterator() as $row) {
            $rowIndex = $row->getRowIndex();
            $cellA = $sheet->getCell('A'.$rowIndex)->getValue();
            $cellB = $sheet->getCell('B'.$rowIndex)->getValue();
            if (null !== $cellA && null !== $cellB) {
                if ($rowIndex > 2) {
                    $data[] = [
                        'categories_id' => $cellB,
                        'categories' => $this->makeSpace($cellA),
                    ];
                }
            }
        }

        return $data;
    }

    private function makeSpace(string $word): string
    {
        $word = str_replace('/', 'et ', $word);
        $word = str_replace(' ', '_', $word);

        return $word;
    }

    private static function gethierarchieLevels(string $level): mixed
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

    public static function getCategoriesById(array $data, string $id): string
    {
        foreach ($data as $item) {
            if ($item['categories_id'] === $id) {
                return $item['categories'];
            }
        }

        return '';
    }

    public static function getIdByCategorie(array $data, string $categorie): string
    {
        foreach ($data as $item) {
            if ($item['categories'] === $categorie) {
                return $item['categories_id'];
            }
        }

        return '';
    }

    public function getCodeConcatenateByID(array $data, string $id): string
    {
        $levels = [];
        $hierarchie = [];
        $levels = $this::gethierarchieLevels($id);
        foreach ($levels as $value) {
            $hierarchie[] = $this::getCategoriesById($data, $value);
        }

        return implode('.', $hierarchie);
    }

    public function getParentIdByChildId(string $ChildId): mixed
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

    private function getCategoriesId($data): array
    {
        $categories_id = [];
        foreach ($data as $item) {
            $categories_id[] = $item['categories_id'];
        }

        return $categories_id;
    }

    public function SaveThemesOnFileJson(array $data, ?string $filePath = null): mixed
    {
        $file = $filePath ?? '/default/path/to/Theme.json';
        $categories_id = $this->getCategoriesId($data);

        $themes = [];
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $y = 1;
        for ($i = 0; $i < count($categories_id); ++$i, ++$y) {
            $themes[] = [
                'id' => $y,
                'code' => $this->getCodeConcatenateByID($data, $categories_id[$i]),
                'externalId' => $categories_id[$i],
                'isSection' => true,
                'parentId' => $this->getParentIdByChildId($categories_id[$i]),
            ];
        }
        $json = json_encode($themes);
        file_put_contents($file, $json);

        return true;
    }
}
