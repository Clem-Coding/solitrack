<?php

namespace App\Repository;

use App\Entity\Visitor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Visitor>
 */
class VisitorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visitor::class);
    }



    public function findTotalDataByMonth($repository, $category = null, $year)
    {

        $qb = $repository->createQueryBuilder('v')
            ->select('MONTH(v.date) AS month', 'SUM(v.count) AS totalData')
            ->groupBy('month')
            ->orderBy('month', 'ASC');

        if ($year) {
            $qb->andWhere('YEAR(v.date) = :year')
                ->setParameter('year', $year);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Here are examples of MySQL native query adaptations to retrieve the total visitor count per day for a specific month:
     * 
     * 1. Retrieve the total count of all visitors per day for March 2025:
     * 
     *    SELECT DAY(v.date) AS day, SUM(v.count) AS totalData
     *    FROM visitors v
     *    WHERE MONTH(v.date) = 3
     *      AND YEAR(v.date) = 2025
     *    GROUP BY day
     *    ORDER BY day ASC;
 
     */

    public function findTotalDataByDayForMonth($repository, $category = null, $year, $month)
    {
        $qb = $this->createQueryBuilder('v')
            ->select('DAY(v.date) AS day', 'SUM(v.count) AS totalData')
            ->where('MONTH(v.date) = :month')
            ->andWhere('YEAR(v.date) = :year')
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->groupBy('day')
            ->orderBy('day', 'ASC');

        return $qb->getQuery()->getResult();
    }



    /**
     * Here are examples of MySQL native query adaptations to retrieve the total visitor count per year:
     * 
     * 1. Retrieve the total count of all visitors per year:
     * 
     *    SELECT YEAR(v.date) AS year, SUM(v.count) AS totalData
     *    FROM visitors v
     *    GROUP BY year
     *    ORDER BY year ASC;
     */
    public function findTotalDataByYear($repository, $category = null)
    {
        $qb = $this->createQueryBuilder('v')
            ->select('YEAR(v.date) AS year', 'SUM(v.count) AS totalData')
            ->groupBy('year')
            ->orderBy('year', 'ASC');

        return $qb->getQuery()->getResult();
    }



    // SET lc_time_names = 'fr_FR';
    // SELECT DATE_FORMAT(v.date, "%W %d %M %Y") AS day, SUM(v.count) AS count
    // FROM visitors v
    // GROUP BY day
    // ORDER BY count DESC
    // LIMIT 1
    public function getRecordWeightDay()
    {
        $this->getEntityManager()->getConnection()->executeStatement("SET lc_time_names = 'fr_FR';");

        $sql = 'SELECT DATE_FORMAT(v.date, "%W %d %M %Y") AS day, SUM(v.count) AS count
            FROM visitors v
            GROUP BY day
            ORDER BY count DESC
            LIMIT 1;';

        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAssociative();
    }
}
