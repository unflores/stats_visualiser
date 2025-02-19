<?php

namespace App\Command;

use App\Script\IngestTheme;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'importThemes',
    description: 'Parse themes from csvs and import them into the database',
)]
class CommandIngestTheme extends Command
{
    private $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('getjson', InputArgument::OPTIONAL, 'obtenir les données json')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('getjson');

        $IngestTheme = new IngestTheme();

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        // Assurez-vous que le chemin du fichier est correct et que le fichier est au format .xlsx
        $filePath = $this->projectDir.'/public/File/CITEPA.xlsx'; // Remplacez par le chemin correct
        if (!file_exists($filePath)) {
            $io->error('Le fichier n\'existe pas : '.$filePath);

            return Command::FAILURE;
        }

        try {
            $result = $IngestTheme->SaveThemesOnFileJson($IngestTheme->GetJsonDataFileXlsx($filePath), $this->projectDir.'/public/File/themes.json');
            $io->success('Résultat: '.json_encode($result));
        } catch (\Exception $e) {
            $io->error('Erreur lors de la lecture du fichier : '.$e->getMessage());

            return Command::FAILURE;
        }

        $io->success('List how many themes have been added '.count($IngestTheme->GetJsonDataFileXlsx($filePath)).' themes');

        return Command::SUCCESS;
    }
}
