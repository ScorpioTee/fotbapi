<?php

namespace App\Command;

use App\Entity\Competition;
use App\Entity\CompetitionTable;
use App\Service\FacrParserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'facr:update-tables',
    description: 'Update competitions.',
)]
class FacrUpdateCommand extends Command
{
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
            ->addOption('competition', 'c', InputOption::VALUE_OPTIONAL, 'UUID of competition')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // $this->facrParserService->loadMatchStatistics();
        // return Command::SUCCESS;

        $competitionTableRepository = $this->entityManager->getRepository(CompetitionTable::class);
        $competitionRepository = $this->entityManager->getRepository(Competition::class);

        if ($req = $input->getOption('competition')) {
            $competitions = $competitionRepository->findBy(['req' => $req]);
        } else {
            $competitions = $competitionRepository->findAll();
        }
        foreach($competitions as $competition)
        {
            $this->facrParserService->competitionTable($competition->getReq());
            if ($this->facrParserService->getData() === null) {
                $io->error('Something was wrong :( Do you have right uuid of competition?');
                return Command::FAILURE;
            }
            $competitionTableRepository->updateCompetitionTable($this->facrParserService->getData());
            $io->info(sprintf('Competition `%s` was updated.', $competition->getName()));
            $this->facrParserService->resetData();
        }
        $io->info('All competitions were updated.');

        return Command::SUCCESS;
    }

}
