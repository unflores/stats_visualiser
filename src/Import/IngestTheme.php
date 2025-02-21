<?php

namespace App\Import;

use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class IngestTheme
{
    private $projectDir;
    private $entityManager;
    private $themeRepository;

    public function __construct(EntityManagerInterface $entityManager, string $projectDir)
    {
        $this->entityManager = $entityManager;
        $this->themeRepository = $entityManager->getRepository(Theme::class);
        $this->projectDir = $projectDir;
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

        for ($i = 0; $i < count($categories_id); ++$i) {
            $themes_json[] = [
                'code' => $this->getCodeConcatenateByCategorieId($themes, $categories_id[$i]),
                'externalId' => $categories_id[$i],
                'isSection' => true,
                'parentId' => $this->getParentIdByChildId($categories_id[$i]),
            ];
        }

        return json_encode($themes_json);
    }

    public function SaveThemesOnDatabase(): bool
    {
        $array_themes = [];

        $excel_file = $this->projectDir.'/public/File/emissions_GES_structure.xlsx';
        $ingestTheme = new IngestTheme($this->entityManager, $this->projectDir);
        $array_themes = $ingestTheme->PrepareThemesForDatabase($ingestTheme->GetThemesFromExcelFile($excel_file));
        $array_themes = json_decode($array_themes, true);

        if (null == $this->findParentId()) {
            foreach ($array_themes as $theme) {
                $this->entityManager->persist(
                    (new Theme())
                        ->setCode($theme['code'])
                        // ->setParentId($theme['parentId'])
                        ->setExternalId($theme['externalId'])
                        ->setIsSection($theme['isSection'])
                );
                $this->entityManager->flush();
                break;
            }

            return true;
        }

        return false;
    }

    private function findParentId(string $externalId = ''): mixed
    {
        if (empty($this->themeRepository->findAll())) {
            // la table est vide
            // premier enregisterement
            // parentId=null
            return null;
        } else {
            $column = null;

            // donc on a le premier champs
            // connaitre le nombre de champs
            // count = ...
            return $this->findParentIdByExternalId(); // return parentid
        }
    }

    private function findParentIdByExternalId(?string $externalId = ''): int
    {
        $value = $externalId;
        // on recuperer le premier champs
        // note champs courants
        // note : connaitre le nombre total de champs
        // note all =
        // check if exist
        // search it
        // return its id

        return 0;
    }
}
