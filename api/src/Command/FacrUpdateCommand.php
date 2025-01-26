<?php

namespace App\Command;

use App\Entity\Competition;
use App\Entity\CompetitionTable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'facr:update',
    description: 'Update competitions.',
)]
class FacrUpdateCommand extends Command
{
    private const TABLE_URL_PATTERN =  'https://is1.fotbal.cz/souteze/tabulky-souteze.aspx?req=%s&sport=fotbal';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addOption('competition', 'c', InputOption::VALUE_OPTIONAL, 'Code of competition')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $competitionTableRepository = $this->entityManager->getRepository(CompetitionTable::class);
        $competitionRepository = $this->entityManager->getRepository(Competition::class);
        $competitions = $competitionRepository->findAll();
        foreach($competitions as $competition)
        {
            $data = $this->loadTable($competition->getReq());
            $competitionTableRepository->updateCompetitionTable($data);
        }

        if ($input->getOption('competition')) {
            return Command::SUCCESS;
        }

        $io->info('Competitions updated.');

        return Command::SUCCESS;
    }

    private function loadTable($uuid)
    {
        $tableUrl = sprintf(self::TABLE_URL_PATTERN, $uuid);
        $data = [];

        $domTable = new \DomDocument();
        $content = file_get_contents($tableUrl);
        try {
            libxml_use_internal_errors(true);
            $domTable->loadHTML($content);
        } catch(\Exception $e) {
            return null;
        }
        if ($domTable === null) {
            return null;
        }
        $table = $domTable->getElementsByTagName('table');
        $tableItem = $table->item(0);
        if ($tableItem === null) {
            echo "Table not published ...";
            return null;
        }
        $rows = $tableItem->getElementsByTagName('tr');
        foreach($rows as $row) {
            if (strpos($row->nodeValue, 'Rk.') !== false) continue;

            $col = $row->getElementsByTagName('td');
            $team = trim(preg_replace('/\([0-9]+\)/','',$col->item(1)->nodeValue));
            $scoreArray = explode(':', $col->item(6)->nodeValue);
            $data[] = [
                'req' => $uuid,
                'position' => $col->item(0)->nodeValue,
                'club' => trim($team),
                'win' => $col->item(3)->nodeValue,
                'draw' => $col->item(4)->nodeValue,
                'lost' => $col->item(5)->nodeValue,
                'goalsScored' => $scoreArray[0],
                'goalsReceived' => $scoreArray[1],
                'points' => $col->item(7)->nodeValue,
            ];
        }

        return $data;
    }
}
