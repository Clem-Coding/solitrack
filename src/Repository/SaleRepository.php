<?php

namespace App\Repository;

use App\Entity\Sale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sale>
 */
class SaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sale::class);
    }

    /**
     * Retrieve the total data per month for a given year and an optional category filter.
     *
     * 1. Retrieve the total data per month for all categories:
     * 
     *    SELECT MONTH(s.created_at) AS month, SUM(s.total_price) AS totalData
     *    FROM sales s
     *    WHERE YEAR(s.created_at) = 2025
     *    GROUP BY month
     *    ORDER BY month ASC;
     * 
     * 2. Retrieve the total data per month for the "bar" category (id = 4):
     * 
     *    SELECT MONTH(s.created_at) AS month, SUM(s.total_price) AS totalData
     *    FROM sales s
     *    JOIN categories c ON s.category_id = c.id
     *     JOIN sales_items si ON si.sale_id = s.id
     *    WHERE YEAR(s.created_at) = 2025
     *      AND c.id = 4
     *    GROUP BY month
     *    ORDER BY month ASC;
     */

    public function findTotalDataByMonth(?string $year, ?string $type, ?string $category): array
    {

        $qb = $this->createQueryBuilder('s')
            ->select(
                'MONTH(s.createdAt) AS month',
                $type === "bar"
                    ? 'SUM(si.price) AS totalData'
                    : 'SUM(s.totalPrice) + SUM(COALESCE(s.pwywAmount, 0)) AS totalData'
            )

            ->where('YEAR(s.createdAt) = :year')
            ->setParameter('year', $year)
            ->groupBy('month')
            ->orderBy('month', 'ASC');

        if ($type === "bar") {
            $qb->innerJoin('s.salesItems', 'si')
                ->innerJoin('si.category', 'c')
                ->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', 4);
        }

        return $qb->getQuery()->getResult();
    }


    public function findTotalDataByDayForMonth(?int $year, ?int $month, ?string $type, ?string $category = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->select(
                'DAY(s.createdAt) AS day',
                $type === "bar"
                    ? 'SUM(si.price) AS totalData'
                    : 'SUM(s.totalPrice) + SUM(COALESCE(s.pwywAmount, 0)) AS totalData'
            )
            ->where('YEAR(s.createdAt) = :year')
            ->andWhere('MONTH(s.createdAt) = :month')
            ->setParameter('year', $year)
            ->setParameter('month', $month);

        if ($type === "bar") {
            $qb->innerJoin('s.salesItems', 'si')
                ->innerJoin('si.category', 'c')
                ->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', 4);
        }

        $qb->groupBy('day')
            ->orderBy('day', 'ASC');

        return $qb->getQuery()->getResult();
    }



    public function findTotalDataByYear(?string $type = null, ?string $category = null): array

    {
        $qb = $this->createQueryBuilder('s')
            ->select(
                'YEAR(s.createdAt) AS year',
                $type === "bar"
                    ? 'SUM(si.price) AS totalData'
                    : 'SUM(s.totalPrice) + SUM(COALESCE(s.pwywAmount, 0)) AS totalData'
            )
            ->groupBy('year')
            ->orderBy('year', 'ASC');

        if ($type === "bar") {
            $qb->innerJoin('s.salesItems', 'si')
                ->innerJoin('si.category', 'c')
                ->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', 4);
        }

        return $qb->getQuery()->getResult();
    }





    // SELECT zipcode_customer, COUNT(*) AS visitorCount
    // FROM sales
    // WHERE zipcode_customer IS NOT NULL AND zipcode_customer != ''
    // GROUP BY zipcode_customer;


    public function countVisitorsByCity(): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.customer_city AS city, s.zipcodeCustomer AS zipcode, COUNT(s.id) AS visitorCount')
            ->where('s.customer_city IS NOT NULL')
            ->andWhere('s.customer_city != \'\'')
            ->andWhere('s.zipcodeCustomer IS NOT NULL')
            ->andWhere('s.zipcodeCustomer != \'\'')
            ->groupBy('s.customer_city, s.zipcodeCustomer')
            ->getQuery()
            ->getResult();
    }




    // SET lc_time_names = 'fr_FR';
    // SELECT DATE_FORMAT(s.created_at, "%W %d %M %Y") AS day, SUM(s.total_price) AS total_price
    // FROM sales s
    // GROUP BY day
    // ORDER BY total_price DESC
    // LIMIT 1;
    public function getRecordWeightDay()
    {
        $this->getEntityManager()->getConnection()->executeStatement("SET lc_time_names = 'fr_FR';");

        $sql = 'SELECT DATE_FORMAT(s.created_at, "%W %d %M %Y") AS day, SUM(s.total_price) AS total_price
            FROM sales s
            GROUP BY day
            ORDER BY total_price DESC
            LIMIT 1;';

        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAssociative();
    }
}
