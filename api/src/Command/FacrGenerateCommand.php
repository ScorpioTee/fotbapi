<?php

namespace App\Command;

use App\Entity\Competition;
use App\Entity\Season;
use App\Service\FacrParserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'facr:generate-season',
    description: 'Generate new season.',
)]
class FacrGenerateCommand extends Command
{
    private const SEASON_CMD = 'season';
    private const COMPETITION_CMD = 'competition';
    private const AVAILABLE_COMMANDS = [
        self::SEASON_CMD,
        self::COMPETITION_CMD,
    ];

    private EntityManagerInterface $entityManager;
    private FacrParserService $facrParserService;

    public function __construct(EntityManagerInterface $entityManager, FacrParserService $facrParserService)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->facrParserService = $facrParserService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('cmd', InputArgument::REQUIRED, 'Season | Competition.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $cmd = $input->getArgument('cmd');
        if (!in_array($cmd, self::AVAILABLE_COMMANDS)) {
            $io->error('Command not supported.');
            return Command::FAILURE;
        }

        $seasonName = $this->generateSeasonName();
        $seasonRepository = $this->entityManager->getRepository(Season::class);
        $season = $seasonRepository->findByName($seasonName);
        if ($cmd === self::SEASON_CMD) {
            if ($season !== null) {
                $io->error(sprintf('Season `%s` already exists.', $seasonName));
                return Command::FAILURE;
            }
            $seasonRepository->createNewSeason($seasonName);
            $io->info(sprintf('Season `%s` created.', $seasonName));
        }

        if ($cmd === self::COMPETITION_CMD) {
            if ($season === null) {
                $io->error('You must create season before competition.');
                return Command::FAILURE;
            }
            $competitionRepository = $this->entityManager->getRepository(Competition::class);
            $this->facrParserService->generateCompleteCompetitionData();
            $competitionRepository->insertCompetitions($this->facrParserService->getData(), $season);

            $io->info('Competitions were generated.');
            return Command::SUCCESS;
        }

        return Command::SUCCESS;
    }

    private function generateSeasonName(): string
    {
        $now = new \DateTime();
        if ($now->format('m') >= 7) {
            $year1 = $now->format('Y');
            $year2 = (int) $now->format('Y') + 1;
        } else {
            $year1 = (int) $now->format('Y') - 1;
            $year2 = $now->format('Y');
        }
        return sprintf('%s/%s', $year1, $year2);
    }
}
