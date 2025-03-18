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

    public function findTotalWeightByMonth($year = null)
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




    public function findTotalWeightByDayForMonth($year, $month)
    {
        return $this->createQueryBuilder('si')
            ->select('DAY(s.createdAt) as day', 'SUM(si.weight) as totalWeight')
            ->innerJoin('si.sale', 's') // Joindre la table Sale (assurez-vous que "sale" est le bon nom de relation dans l'entité SalesItem)
            ->where('YEAR(s.createdAt) = :year')
            ->andWhere('MONTH(s.createdAt) = :month')
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->setParameter('year', $year)
            ->setParameter('month', $month)
            ->getQuery()
            ->getResult();
    }



    public function findTotalWeightByYear()
    {
        return $this->createQueryBuilder('si')
            ->select('YEAR(s.createdAt) AS year', 'SUM(si.weight) AS totalWeight')
            ->innerJoin('si.sale', 's')
            ->groupBy('year')
            ->orderBy('year', 'ASC')
            ->getQuery()
            ->getResult();
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
