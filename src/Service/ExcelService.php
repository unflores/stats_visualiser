<?php

namespace App\Service;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class ExcelService 
{
/**
 * class for extract data from excel file
 */
        public function extractColumnFromExcel(string $filePath, $columnLetter): array
        {
                $reader = ReaderEntityFactory::createXLSXReader();
                $reader->open($filePath);
                $columnIndex = ord(strtoupper($columnLetter)) - ord('A');
                $columnData = [];

                foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                        $cells = $row->getCells();
                        if(isset($cells[$columnIndex])){
                                $columnData [] = $cells[$columnIndex] ->getValue();
                        }
                }
                }
                $reader->close();
                return $columnData;

        } 

}