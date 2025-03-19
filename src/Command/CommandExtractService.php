<?php

namespace App\Command;

use App\Imports\Themes\ExtractService;
use App\ThemeReader;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ExtractService',
    description: 'Parse themes from excel and import them into the database',
)]
class CommandExtractService extends Command
{
    private $projectDir;        
    private $entityManager;
    private ?Worksheet $sheet = null;

    public function __construct(string $projectDir, EntityManagerInterface $entityManager)
    {
        $this->projectDir = $projectDir;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    // protected function configure(): void
    // {
    //     $this
    //         ->addArgument('extracthemes', InputArgument::OPTIONAL, 'extract and save themes into database themes');
    //     $this    ->addArgument('extract1', InputArgument::OPTIONAL, 'extract and save themes into database themes');
    //     $this    ->addArgument('extract2', InputArgument::OPTIONAL, 'extract and save themes into database themes');


    // }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $excle_file = $this->projectDir.'/var/import-data/emissions_GES_structure.xlsx';
        if (!file_exists($excle_file)) {
            $io->error('file does not exist');
            return Command::FAILURE;
        }
        $spreadsheet = IOFactory::load($excle_file);
        $this->sheet = $spreadsheet->getActiveSheet();
        $readtheme = new ThemeReader($this->sheet);
        $themes = $readtheme->ingest();
        $io->info(count($themes).' themes extracted');
        $extract_service = new ExtractService($this->entityManager, $this->sheet);

        $prepared_themes = $extract_service->PrepareThemesForDatabase($themes);
        $saved_themes_count = $extract_service->SaveThemesOnDatabase($prepared_themes);
        $io->info("$saved_themes_count themes were upserted successfuly");
        
        

        // $spreadsheet = IOFactory::load($excle_file);
        // $this->sheet = $spreadsheet->getActiveSheet();
        // $themes = $this->readTheme();
        // $io->info(count($themes).' themes extracted');
        // $this->sheet = $spreadsheet->getActiveSheet();
        // $extracthemes = $input->getArgument('extracthemes');
        // $extract_service = new ExtractService($this->entityManager, $this->sheet);

       // $io->info('sheet');
        // $readTheme =  new ThemeReader($worksheet);
        

        // if ($extracthemes) {
        //     $excel_file = $this->projectDir.'/var/import-data/emissions_GES_structure.xlsx';

        //     if (!file_exists($excel_file)) {
        //         $io->error('file does not exist');

        //         return Command::FAILURE;
        //     }
        //     $spreadsheet = IOFactory::load($excel_file);
        //     $sheet = $spreadsheet->getActiveSheet();
            

        //     try {
        //         $themes = $readTheme->ingest();
        //        // $extracted_themes = $extract_service->GetThemesFromExcelFile($excel_file);
        //         $io->info(count($themes).' themes extracted');
        //         $prepared_themes = $extract_service->PrepareThemesForDatabase($themes);
        //         $saved_themes_count = $extract_service->SaveThemesOnDatabase($prepared_themes);

        //         $io->info("$saved_themes_count themes were upserted successfuly");
        //     } catch (\Exception $e) {
        //         $io->error('File Excel failed to read : '.$e->getMessage());

        //         return Command::FAILURE;
        //     }

        //     return Command::SUCCESS;
        // }

        return Command::SUCCESS;
    }


}
