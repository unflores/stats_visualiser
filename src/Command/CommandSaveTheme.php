<?php

namespace App\Command;

use App\Script\SaveTheme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'SaveThemes',
    description: 'Save themes  into the database',
)]
class CommandSaveTheme extends Command
{
    private $projectDir;

    private $entityManager;
    public function __construct(string $projectDir, EntityManagerInterface $entityManager)
    {
        $this->projectDir = $projectDir;
        $this->entityManager = $entityManager;
        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->addArgument('save', InputArgument::OPTIONAL, 'enregistrer les themes dans la base de données')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('save');
        $saveTheme = new SaveTheme();

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
            $filePath = $this->projectDir.'/public/File/themes.json';
            if (!file_exists($filePath)) {
                $io->error('Le fichier n\'existe pas : '.$filePath);

                return Command::FAILURE;
            }

            $file = $this->projectDir.'/public/File/themes.json';
            $result = $saveTheme->saveOnDatabase($file);
            if ($result) {
                $io->info('le nombre des themes enregistrés ');
                $io->info('le nombre des themes  : '.count($result).' enregistrés');
            }

            return Command::SUCCESS;
        }

        return Command::SUCCESS;
    }
}
