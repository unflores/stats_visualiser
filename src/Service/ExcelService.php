<?php

namespace App\Service;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class ExcelService
{
    public function extractColumnFromExcel(string $filePath, string $columnLetter = 'A'): array
    {
        if (!file_exists($filePath)) {
            return ['error' => 'Fichier non trouvé'];
        }

        $data = [];
        $reader = ReaderEntityFactory::createReaderFromFile($filePath);

        try {
            $reader->open($filePath);

            $columnIndex = ord(strtoupper($columnLetter)) - ord('A');

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $cells = $row->getCells();

                    if (isset($cells[$columnIndex])) {
                        $value = trim($cells[$columnIndex]->getValue());
                        $data[] = $value;
                    }
                }
                break; 
            }

            $reader->close();
        } catch (\Exception $e) {
            return ['error' => 'Erreur lors de la lecture du fichier'];
        }

        return $data;
    }
}
