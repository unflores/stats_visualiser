<?php 


namespace App\Script;
use App\Entity\Theme;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class IngestTheme
{
   
    public function GetJsonDataFileXlsx(string $file): mixed
    {
        $data = [];
        $code = ['code'=>null,'externalId'=>null];
        
        if (!file_exists($file)) {
            return ["File not found"];
        }
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();

        $newSpreadsheet = new Spreadsheet();
        $newSheet = $newSpreadsheet->getActiveSheet();

        foreach ($sheet->getRowIterator() as  $row) {
            $rowIndex  = $row->getRowIndex();
            $cellA = $sheet->getCell('A' . $rowIndex)->getValue();
            $cellB = $sheet->getCell('B' . $rowIndex)->getValue();
            if($cellA !== null && $cellB !== null){                
                if($rowIndex > 2){                   
                    $data[] = [
                            "categories_id" => $cellB,
                            "categories" => $this->makeSpace($cellA),
                    ];                                  
                }                
            }           
        }
        $theme = array();
        $code = $this->getCodeConcatenateByID($data, "V0.1"); 
        $externalId = $this::getIdByCategorie($data, "par_gaz_à_effet_de_serre");
        $parentId = $this->getParentIdByChildId("V0.1");

        $theme = [
            'id'=>1,
            'code' => $code,
            'externalId' => $externalId,
            'isSection' => true,
            'parentId' => $parentId,
        ];

        $external = [];
        $external[] = $this->makeExternalValue($data, id: "V0.1.5");   
        return [$external,$theme, $data];
    }

    private function makeSpace(string $word):string {
        $word = str_replace('/', 'et ', $word);
        $word = str_replace(' ', '_', $word);
        return $word;
    }

    public function makeExternalValue(array $data, string $id): mixed
    {
        //initialisation des variables
        $hierarchie = [];
        $hierarchie = $this->getParentIdByChildId($id);
        //-cherche la valeur  de V0  dans le tableau data
        
        return [$hierarchie];    
    }
    private static function gethierarchieLevels(string $level): mixed
    {
        $hierarchie = [];
        $check_dot = strpos($level, '.');

        if ($check_dot !== false) {
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
    public  function getCodeConcatenateByID(array $data, string $id):string
    {
        $levels = [];
        $hierarchie = [];
        $levels = $this::gethierarchieLevels($id);
        foreach ($levels as $value) {
            $hierarchie[] = $this::getCategoriesById($data, $value);
        }
        return  implode('.', $hierarchie);
    }
    public function getParentIdByChildId(string $ChildId): mixed
    {
        $check_dot = strpos($ChildId, '.');

        if ($check_dot !== false) {
            $level_array = explode('.', $ChildId);
            while (!empty($level_array)) {
                $hierarchie[] = implode('.', $level_array);
                array_pop($level_array);
            }
            $level_array =  array_reverse($hierarchie);
            return $level_array[count($level_array) - 2];
        } else {
            return "null";
        }
    }

}