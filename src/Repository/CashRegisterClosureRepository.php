<?php

namespace App\Repository;

use App\Entity\CashRegisterClosure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CashRegisterClosure>
 */
class CashRegisterClosureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CashRegisterClosure::class);
    }


    // SELECT 
    //     c.id,
    //     c.closed_at,
    //     c.closing_cash_amount,
    //     c.discrepancy,
    //     u.first_name AS closed_by_name,
    //     'SUM(sales.totalPrice + COALESCE(sales.pwywAmount, 0)) AS totalSales',
    //     'SUM(sales.cashAmount) AS totalCash',
    //     'SUM(sales.cardAmount) AS totalCard'
    // FROM 
    //     cash_register_closures c
    // LEFT JOIN 
    //     users u ON c.closed_by_id = u.id
    // LEFT JOIN 
    //     cash_register_sessions s ON s.id = c.cash_register_session_id
    // LEFT JOIN 
    //     sales sal ON sal.cash_register_session_id = s.id
    // GROUP BY 
    //     c.id, c.closed_at, c.closing_cash_amount, c.discrepancy, u.first_name
    // ORDER BY 
    //     c.closed_at DESC
    // LIMIT 1;


    public function findLastClosureWithUser(): ?array
    {
        return $this->createQueryBuilder('c')
            ->select(
                'c.id',
                'c.closedAt',
                'c.closingCashAmount',
                'c.discrepancy',
                'c.note',
                'u.firstName AS closedByName',
                'SUM(sales.totalPrice + COALESCE(sales.pwywAmount, 0)) AS totalSales',
                'SUM(CASE WHEN payment.method = \'cash\' THEN payment.amount ELSE 0 END) AS totalCash',
                'SUM(CASE WHEN payment.method = \'card\' THEN payment.amount ELSE 0 END) AS totalCard',
                'SUM(sales.pwywAmount) AS totalPwyw'
            )
            ->leftJoin('c.closedBy', 'u')
            ->leftJoin('c.cashRegisterSession', 'session')
            ->leftJoin('session.sales', 'sales')
            ->leftJoin('sales.payments', 'payment')
            ->groupBy('c.id', 'c.closedAt', 'c.closingCashAmount', 'c.discrepancy', 'u.firstName')
            ->orderBy('c.closedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }




    //    /**
    //     * @return CashRegisterClosure[] Returns an array of CashRegisterClosure objects
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

    //    public function findOneBySomeField($value): ?CashRegisterClosure
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
