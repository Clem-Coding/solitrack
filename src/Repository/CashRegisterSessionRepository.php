<?php

namespace App\Repository;

use App\Entity\CashRegisterSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CashRegisterSession>
 */
class CashRegisterSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CashRegisterSession::class);
    }


    // SELECT c.*
    // FROM cash_register_sessions c
    // LEFT JOIN cash_register_closures cl ON cl.cash_register_session_id = c.id
    // WHERE cl.closed_at IS NULL OR cl.id IS NULL
    // LIMIT 1;

    public function findAnyOpenSession(): ?CashRegisterSession
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.cashRegisterClosures', 'cl')
            ->andWhere('cl.closedAt IS NULL OR cl.id IS NULL')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    //    /**
    //     * @return CashRegisterSession[] Returns an array of CashRegisterSession objects
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

    //    public function findOneBySomeField($value): ?CashRegisterSession
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
