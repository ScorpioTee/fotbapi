<?php

namespace App\Repository;

use App\Entity\Competition;
use App\Entity\Season;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Competition>
 */
class CompetitionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Competition::class);
    }

    public function insertCompetitions(array $competitionsData, Season $season)
    {
        foreach($competitionsData as $uuid => $competitionData)
        {
            $competition = $this->findByReq($uuid);
            if ($competition === null) {
                $this->create($season, $competitionData['name'], $uuid);
            } else {
                // DO NOTHING - ALREADY EXISTS
            }
        }
    }

    public function create(Season $season, string $name, string $req, ?string $code = null)
    {
        $competition = new Competition();
        $competition->setSeason($season);
        $competition->setName($name);
        $competition->setFacrCode($code);
        $competition->setReq($req);
        $competition->setCreatedAt(null);
        $this->getEntityManager()->persist($competition);
        $this->getEntityManager()->flush();
    }

    public function findByReq(string $req): ?Competition
    {
        return $this->findOneBy(['req' => $req]);
    }

    //    /**
    //     * @return Competition[] Returns an array of Competition objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Competition
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
