<?php

namespace App\Command;

use App\Import\IngestTheme;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'importThemes',
    description: 'Parse themes from excel and import them into the database',
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
            ->addArgument('savethemes', InputArgument::OPTIONAL, 'save themes on database themes')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('savethemes');

        $ingestTheme = new IngestTheme();
        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
            $excel_file = $this->projectDir.'/public/File/emissions_GES_structure.xlsx';

            if (!file_exists($excel_file)) {
                $io->error('file does not exist');
                return Command::FAILURE;
            }
            try {
                $themes = $ingestTheme->PrepareThemesForDatabase($ingestTheme->GetThemesFromExcelFile($excel_file));
                $themes = json_decode($themes);
                $io->success('Résultat: '.json_encode($themes));

            } catch (\Exception $e) {
                $io->error('Erreur lors de la lecture du fichier : '.$e->getMessage());
                return Command::FAILURE;
            }
            return Command::SUCCESS;
        }
        return Command::SUCCESS;
    }
}
