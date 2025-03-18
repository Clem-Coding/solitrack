<?php

namespace App\Repository;

use App\Entity\SalesItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SalesItem>
 */
class SalesItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SalesItem::class);
    }


    // SELECT MONTH(s.created_at) AS month, 
    // SUM(si.weight) AS totalWeight
    // FROM sales_items si
    // LEFT JOIN sales s ON si.sale_id = s.id
    // GROUP BY month


    // ex:
    // month 	totalWeight 	
    // 3 	    666.21

    public function findTotalSalesByMonth($year = null)
    {
        $qb = $this->createQueryBuilder('si')
            ->select('MONTH(s.createdAt) AS month', 'SUM(si.weight) AS totalWeight')
            ->innerJoin('si.sale', 's')  // Jointure avec la table sale
            ->groupBy('month')
            ->orderBy('month', 'ASC');

        if ($year) {
            $qb->andWhere('YEAR(s.createdAt) = :year')
                ->setParameter('year', $year);
        }

        return $qb->getQuery()->getResult();
    }


    //    /**
    //     * @return SalesItem[] Returns an array of SalesItem objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SalesItem
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
