<?php

namespace App\Command;

use App\Entity\Theme;
use App\Imports\Themes\ExtractService;
use Doctrine\ORM\EntityManagerInterface;
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
    private $themeRepository;

    public function __construct(string $projectDir, EntityManagerInterface $entityManager)
    {
        $this->projectDir = $projectDir;
        $this->entityManager = $entityManager;
        $this->themeRepository = $entityManager->getRepository(Theme::class);
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('extracthemes', InputArgument::OPTIONAL, 'extract and save themes into database themes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $extracthemes = $input->getArgument('extracthemes');
        $extract_service = new ExtractService($this->entityManager);

        if ($extracthemes) {
            $excel_file = $this->projectDir.'/public/File/emissions_GES_structure.xlsx';

            if (!file_exists($excel_file)) {
                $io->error('file does not exist');

                return Command::FAILURE;
            }

            try {
                $extracted_themes = $extract_service->GetThemesFromExcelFile($excel_file);
                $io->info(count($extracted_themes).' themes extracted');
                $prepared_themes = $extract_service->PrepareThemesForDatabase($extracted_themes);
                $saved_themes_count = $extract_service->SaveThemesOnDatabase($prepared_themes);

                $io->info("$saved_themes_count themes were upserted successfuly");
            } catch (\Exception $e) {
                $io->error('File Excel failed to read : '.$e->getMessage());

                return Command::FAILURE;
            }

            return Command::SUCCESS;
        }

        return Command::SUCCESS;
    }
}
