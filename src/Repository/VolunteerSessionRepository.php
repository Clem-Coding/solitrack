<?php

namespace App\Repository;

use App\Entity\VolunteerSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VolunteerSession>
 */
class VolunteerSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VolunteerSession::class);
    }


    public function findLastThreeSessions(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.startDatetime', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

 


    // trouve toutes les session ou l'utilisateur est inscrit

    //    /**
    //     * @return VolunteerSession[] Returns an array of VolunteerSession objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?VolunteerSession
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
