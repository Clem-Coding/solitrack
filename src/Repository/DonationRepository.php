<?php

namespace App\Repository;

use App\Entity\Donation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection as CollectionsArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Parameter;


/**
 * @extends ServiceEntityRepository<Donation>
 */
class DonationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Donation::class);
    }

    //An exemple of MySQL native query adaptation to retrieve the total weight of donations for today:
    // SELECT SUM(weight) AS total_weight
    // FROM donations
    // WHERE created_at >= CURDATE();

    public function getTotalWeightForToday(): ?float
    {
        return $this->createQueryBuilder('d')
            ->select('SUM(d.weight) as total_weight')
            ->where('d.createdAt >= :today')
            ->setParameter('today', new \DateTime('today'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    // Définition wikipedia :"On parle aussi de valeur ou de variable scalaire pour désigner une valeur ou un contenant 
    //destiné par son type à contenir une valeur atomique. On oppose valeur atomique à valeur composite. 
    //Un entier, un nombre flottant sont des valeurs atomiques. Un tableau ou une table associative sont des valeurs composites. 
    //Une valeur composite est une structure de données composée récursivement ou non de valeurs scalaires.
    // Une chaîne de caractères peut être considérée comme un tableau ou une valeur scalaire selon le langage de programmation."


    //Example MySQL native query adaptation to retrieve the latest donation entry:
    // SELECT d.id, d.weight, c.name AS categoryName
    // FROM donations d
    // LEFT JOIN categories c ON d.category_id = c.id
    // ORDER BY d.created_at DESC
    // LIMIT 1;


    public function getLatestEntry(): ?array
    {
        return $this->createQueryBuilder('d')
            ->select('d.id', 'd.weight', 'c.name AS categoryName')
            ->leftJoin('d.category', 'c')
            ->orderBy('d.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    // public function findAvailableMonths(): array
    // {
    //     return $this->createQueryBuilder('d')
    //         ->select("DISTINCT SUBSTRING(d.createdAt, 1, 7) AS month") // YYYY-MM
    //         ->orderBy('month', 'DESC')
    //         ->getQuery()
    //         ->getResult();
    // }

    public function getMonthsSinceCreation(): array
    {
        $sql = "WITH RECURSIVE month_sequence AS (
            SELECT DATE_FORMAT(MIN(created_at), '%Y-%m-01') AS month
            FROM donations
            WHERE created_at IS NOT NULL
            UNION ALL
            SELECT DATE_FORMAT(DATE_ADD(month, INTERVAL 1 MONTH), '%Y-%m-01')
            FROM month_sequence
            WHERE DATE_ADD(month, INTERVAL 1 MONTH) <= CURDATE()
        )
        SELECT DATE_FORMAT(month, '%Y-%m') AS month
        FROM month_sequence
        ORDER BY month DESC;
        ";

        $connection = $this->getEntityManager()->getConnection();
        $stmt = $connection->executeQuery($sql);

        return $stmt->fetchAllAssociative();
    }


    /**
     * Here are examples of MySQL native query adaptations to retrieve the total donation weight:
     * 
     * 1. Retrieve the total weight of all donations for March 2025:
     * 
     *    SELECT SUM(d.weight) AS totalData
     *    FROM donations d
     *    WHERE MONTH(d.created_at) = 3
     *      AND YEAR(d.created_at) = 2025;
     * 
     * 2. Retrieve the total weight of donations for the "clothing" category (id = 1) for March 2025:
     * 
     *    SELECT SUM(d.weight) AS totalData
     *    FROM donations d
     *    JOIN categories c ON d.category_id = c.id
     *    WHERE c.id = 1
     *      AND MONTH(d.created_at) = 3
     *      AND YEAR(d.created_at) = 2025;
     */


    // public function findTotalDataByMonth($repository, $category, $year): array

    // {

    //     $qb = $repository->createQueryBuilder('d')
    //         ->select('MONTH(d.createdAt) AS month', 'SUM(d.weight) AS totalData')
    //         ->groupBy('month')
    //         ->orderBy('month', 'ASC');

    //     if ($year) {
    //         $qb->andWhere('YEAR(d.createdAt) = :year')
    //             ->setParameter('year', $year);
    //     }

    //     // Si un paramètre category est passé, on fait une jointure sur la table Category
    //     if ($category === "vetements") {
    //         $qb->leftJoin('d.category', 'c')
    //             ->andWhere('c.id = :categoryId')
    //             ->setParameter('categoryId', 1);
    //     }

    //     return $qb->getQuery()->getResult();
    // }




    // public function findTotalDataByMonth($type, ?string $category, ?string $year): array
    public function findTotalDataByMonth(?string $year, ?string $type, ?string $category): array

    {
        $qb = $this->createQueryBuilder('d')
            ->select('MONTH(d.createdAt) AS month', 'SUM(d.weight) AS totalData')
            ->groupBy('month')
            ->orderBy('month', 'ASC');

        if ($year) {
            $qb->andWhere('YEAR(d.createdAt) = :year')
                ->setParameter('year', $year);
        }

        if ($category === "vetements") {
            $qb->leftJoin('d.category', 'c')
                ->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', 1);
        }

        return $qb->getQuery()->getResult();
    }



    /**
     * Here are examples of MySQL native query adaptations to retrieve the total donation weight per day for a specific month:
     * 
     * 1. Retrieve the total weight of all donations per day for March 2025:
     * 
     *    SELECT DAY(d.created_at) AS day, SUM(d.weight) AS totalData
     *    FROM donations d
     *    WHERE MONTH(d.created_at) = 3
     *      AND YEAR(d.created_at) = 2025
     *    GROUP BY day
     *    ORDER BY day ASC;
     * 
     * 2. Retrieve the total weight of donations for the "clothing" category (id = 1) per day for March 2025:
     * 
     *    SELECT DAY(d.created_at) AS day, SUM(d.weight) AS totalData
     *    FROM donations d
     *    JOIN categories c ON d.category_id = c.id
     *    WHERE c.id = 1
     *      AND MONTH(d.created_at) = 3
     *      AND YEAR(d.created_at) = 2025
     *    GROUP BY day
     *    ORDER BY day ASC;
     */


    public function findTotalDataByDayForMonth(
        string $year,
        string $month,
        ?string $type = null,
        ?string $category = null
    ): array {
        $qb = $this->createQueryBuilder('d')
            ->select('DAY(d.createdAt) AS day', 'SUM(d.weight) AS totalData')
            ->groupBy('day')
            ->orderBy('day', 'ASC');

        if ($year) {
            $qb->andWhere('YEAR(d.createdAt) = :year')
                ->setParameter('year', $year);
        }
        if ($month) {
            $qb->andWhere('MONTH(d.createdAt) = :month')
                ->setParameter('month', $month);
        }
        if ($category === "vetements") {
            $qb->leftJoin('d.category', 'c')
                ->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', 1);
        }

        return $qb->getQuery()->getResult();
    }


    /**
     * Here are examples of MySQL native query adaptations to retrieve the total donation weight per year:
     * 
     * 1. Retrieve the total weight of all donations per year:
     * 
     *    SELECT YEAR(d.created_at) AS year, SUM(d.weight) AS totalData
     *    FROM donations d
     *    GROUP BY year
     *    ORDER BY year ASC;
     * 
     * 2. Retrieve the total weight of donations for the "clothing" category (id = 1) per year:
     * 
     *    SELECT YEAR(d.created_at) AS year, SUM(d.weight) AS totalData
     *    FROM donations d
     *    JOIN categories c ON d.category_id = c.id
     *    WHERE c.id = 1
     *    GROUP BY year
     *    ORDER BY year ASC;
     */

    public function findTotalDataByYear(?string $type = null, ?string $category = null): array

    {
        $qb = $this->createQueryBuilder('d')
            ->select('YEAR(d.createdAt) AS year', 'SUM(d.weight) AS totalData')
            ->groupBy('year')
            ->orderBy('year', 'ASC');

        if ($category === "vetements") {
            $qb->leftJoin('d.category', 'c')
                ->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', 1);
        }

        return $qb->getQuery()->getResult();
    }


    // SET lc_time_names = 'fr_FR';
    // SELECT DATE(s.created_at) AS day, SUM(weight) AS total_weight
    // FROM sales_items si
    // JOIN sales s ON si.sale_id = s.id
    // GROUP BY day
    // ORDER BY total_weight DESC
    // LIMIT 1;
    public function getRecordWeightDay(): array
    {
        $this->getEntityManager()->getConnection()->executeStatement("SET lc_time_names = 'fr_FR';");

        $sql = 'SELECT DATE_FORMAT(d.created_at, "%W %d %M %Y") AS day, SUM(d.weight) AS total_weight
            FROM donations d
            GROUP BY day
            ORDER BY total_weight DESC
            LIMIT 1;';

        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAssociative();
    }


    public function findTotalWeightForCurrentMonth()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $qb = $this->createQueryBuilder('d')
            ->select('SUM(d.weight) AS totalWeight')
            ->where('MONTH(d.createdAt) = :currentMonth')
            ->andWhere('YEAR(d.createdAt) = :currentYear')
            ->setParameter('currentMonth', $currentMonth)
            ->setParameter('currentYear', $currentYear);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
