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


    /**
     * Here are examples of MySQL native query adaptations to retrieve the total sales item weight:
     * 
     * 1. Retrieve the total weight of all sales items for March 2025:
     * 
     *    SELECT SUM(si.weight) AS totalData
     *    FROM sales_items si
     *    JOIN sales s ON si.sale_id = s.id
     *    WHERE MONTH(s.created_at) = 3
     *      AND YEAR(s.created_at) = 2025;
     * 
     * 2. Retrieve the total weight of sales items for the "clothing" category (id = 1) for March 2025:
     * 
     *    SELECT SUM(si.weight) AS totalData
     *    FROM sales_items si
     *    JOIN sales s ON si.sale_id = s.id
     *    JOIN categories c ON si.category_id = c.id
     *    WHERE c.id = 1
     *      AND MONTH(s.created_at) = 3
     *      AND YEAR(s.created_at) = 2025;
     */


    public function findTotalDataByMonth($repository, $category, $year)
    {
        $qb = $repository->createQueryBuilder('si')
            ->select('MONTH(s.createdAt) AS month', 'SUM(si.weight) AS totalData')
            ->innerJoin('si.sale', 's')
            ->groupBy('month')
            ->orderBy('month', 'ASC');

        if ($year) {
            $qb->andWhere('YEAR(s.createdAt) = :year')
                ->setParameter('year', $year);
        }

        if ($category === "vetements") {
            $qb->leftJoin('si.category', 'c')
                ->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', 1);
        }

        return $qb->getQuery()->getResult();
    }



    /**
     * Here are examples of MySQL native query adaptations to retrieve the total sales item weight per day for a specific month:
     * 
     * 1. Retrieve the total weight of all sales items per day for March 2025:
     * 
     *    SELECT DAY(s.created_at) AS day, SUM(si.weight) AS totalData
     *    FROM sales_items si
     *    JOIN sales s ON si.sale_id = s.id
     *    WHERE MONTH(s.created_at) = 3
     *      AND YEAR(s.created_at) = 2025
     *    GROUP BY day
     *    ORDER BY day ASC;
     * 
     * 2. Retrieve the total weight of sales items for the "clothing" category (id = 1) per day for March 2025:
     * 
     *    SELECT DAY(s.created_at) AS day, SUM(si.weight) AS totalData
     *    FROM sales_items si
     *    JOIN sales s ON si.sale_id = s.id
     *    JOIN categories c ON si.category_id = c.id
     *    WHERE c.id = 1
     *      AND MONTH(s.created_at) = 3
     *      AND YEAR(s.created_at) = 2025
     *    GROUP BY day
     *    ORDER BY day ASC;
     */
    public function findTotalDataByDayForMonth($repository, $category = null, $year = null, $month = null)
    {
        $qb = $repository->createQueryBuilder('si')
            ->select('DAY(s.createdAt) AS day', 'SUM(si.weight) AS totalData')
            ->innerJoin('si.sale', 's')
            ->groupBy('day')
            ->orderBy('day', 'ASC');


        if ($year) {
            $qb->andWhere('YEAR(s.createdAt) = :year')
                ->setParameter('year', $year);
        }
        if ($month) {
            $qb->andWhere('MONTH(s.createdAt) = :month')
                ->setParameter('month', $month);
        }


        if ($category === "vetements") {
            $qb->leftJoin('si.category', 'c')
                ->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', 1);
        }

        return $qb->getQuery()->getResult();
    }



    /**
     * Here are examples of MySQL native query adaptations to retrieve the total sales item weight per year:
     * 
     * 1. Retrieve the total weight of all sales items per year:
     * 
     *    SELECT YEAR(s.created_at) AS year, SUM(si.weight) AS totalData
     *    FROM sales_items si
     *    JOIN sales s ON si.sale_id = s.id
     *    GROUP BY year
     *    ORDER BY year ASC;
     * 
     * 2. Retrieve the total weight of sales items for the "clothing" category (id = 1) per year:
     * 
     *    SELECT YEAR(s.created_at) AS year, SUM(si.weight) AS totalData
     *    FROM sales_items si
     *    JOIN sales s ON si.sale_id = s.id
     *    JOIN categories c ON si.category_id = c.id
     *    WHERE c.id = 1
     *    GROUP BY year
     *    ORDER BY year ASC;
     */


    public function findTotalDataByYear($repository, $category)
    {
        $qb = $this->createQueryBuilder('si')
            ->select('YEAR(s.createdAt) AS year', 'SUM(si.weight) AS totalData')
            ->innerJoin('si.sale', 's')
            ->groupBy('year')
            ->orderBy('year', 'ASC');


        if ($category === "vetements") {
            $qb->leftJoin('si.category', 'c')
                ->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', 1);
        }

        return $qb->getQuery()->getResult();
    }




    // SET lc_time_names = 'fr_FR';
    // SELECT DATE_FORMAT(s.created_at, "%W %d %M %Y") AS day, SUM(weight) AS total_weight
    // FROM sales_items si
    // JOIN sales s ON si.sale_id = s.id
    // GROUP BY day
    // ORDER BY total_weight DESC
    // LIMIT 1;
    public function getRecordWeightDay(): array
    {
        $this->getEntityManager()->getConnection()->executeStatement("SET lc_time_names = 'fr_FR';");

        $sql = 'SELECT DATE_FORMAT(s.created_at, "%W %d %M %Y") AS day, SUM(weight) AS total_weight
            FROM sales_items si
            JOIN sales s ON si.sale_id = s.id
            GROUP BY day
            ORDER BY total_weight DESC
            LIMIT 1;';

        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAssociative();
    }


    // SELECT SUM(si.weight) AS total_weight
    // FROM sales_items si
    // JOIN sales s ON si.sale_id = s.id
    // WHERE MONTH(s.created_at) = 5
    // AND YEAR(s.created_at) = 2025;

    public function findTotalWeightForCurrentMonth()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        return $this->createQueryBuilder('si')
            ->select('SUM(si.weight) AS total_weight')
            ->join('si.sale', 's')
            ->where('MONTH(s.createdAt) = :currentMonth')
            ->andWhere('YEAR(s.createdAt) = :currentYear')
            ->setParameter('currentMonth', $currentMonth)
            ->setParameter('currentYear', $currentYear)
            ->getQuery()
            ->getSingleScalarResult();
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
