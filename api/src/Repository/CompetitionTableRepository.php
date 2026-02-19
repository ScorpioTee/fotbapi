<?php

namespace App\Repository;

use ApiPlatform\Metadata\Exception\RuntimeException;
use App\Entity\Competition;
use App\Entity\CompetitionTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Table>
 */
class CompetitionTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompetitionTable::class);
    }

    public function updateCompetitionTable(?array $data)
    {
        if ($data === null) {
            throw new RuntimeException('Provided data are empty.');
        }
        $this->getEntityManager()->beginTransaction();
        $competitionTableRepository = $this->_em->getRepository(CompetitionTable::class);
        $competitionRepository = $this->_em->getRepository(Competition::class);
        $uuid = array_keys($data)[0];
        $competition = $competitionRepository->findByReq($uuid);
        $competitionTables = $competitionTableRepository->findBy(['competition' => $competition]);
        foreach($competitionTables as $competitionTable)
        {
            $this->getEntityManager()->remove($competitionTable);
        }

        foreach($data[$uuid] as $record)
        {
            $table = new CompetitionTable();
            $table->setCompetition($competition);
            $table->setClub($record['club']);
            $table->setPosition($record['position']);
            $table->setWin($record['win']);
            $table->setDraw($record['draw']);
            $table->setLost($record['lost']);
            $table->setGoalsScored($record['goalsScored']);
            $table->setGoalsReceived($record['goalsReceived']);
            $table->setPoints($record['points']);
            $table->setUpdatedAt(null);
            $this->getEntityManager()->persist($table);
        }
        $this->getEntityManager()->flush();
        $this->getEntityManager()->commit();
    }

    //    /**
    //     * @return Table[] Returns an array of Table objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Table
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
