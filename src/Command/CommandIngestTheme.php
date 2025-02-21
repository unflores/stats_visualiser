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
            ->addArgument('addThemes', InputArgument::OPTIONAL, 'ajouter les themes dans le fichier themes.json')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('addThemes');

        $themes_extractor = new Themes\ExtractService($this->projectDir.'/public/File/themes.json');
        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
            $filePath = $this->projectDir.'/public/File/CITEPA.xlsx';
            if (!file_exists($filePath)) {
                $io->error('Le fichier n\'existe pas : '.$filePath);

                return Command::FAILURE;
            }

            $themes_to_create = $themes_extractor->execute();
            $io->success('Extracted '. count($themes_to_create).' themes');

            $create_themes = new Themes\CreateService();
            $persisted = $create_themes->execute($themes_to_create);

            $io->success('Persisted '. $persisted .' themes');

            return Command::SUCCESS;
        }

        return Command::SUCCESS;
    }
}
