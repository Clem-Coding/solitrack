<?php

namespace App\Repository;

use App\Entity\CashMovement;
use App\Entity\CashRegisterSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CashMovement>
 */
class CashMovementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CashMovement::class);
    }


    public function findBySession(CashRegisterSession $session): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.cashRegisterSession = :session')
            ->setParameter('session', $session)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return CashMovement[] Returns an array of CashMovement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CashMovement
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
