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


    public function getTotalWeightForToday(): ?float
    {
        return $this->createQueryBuilder('d')
            ->select('SUM(d.weight) as total_weight')
            ->where('d.createdAt >= :today') // >= s'assure que ce soit à partir de minuit et toute la journée et pas uniquement minuit
            ->setParameter('today', new \DateTime('today'))
            ->getQuery()
            ->getSingleScalarResult(); // renvoit une valeur scalaire qui est une valeur "simple" et pas un tableau ou un objet ou collection, utile dans le cas du poids total
    }

    // Définition wikipedia :"On parle aussi de valeur ou de variable scalaire pour désigner une valeur ou un contenant 
    //destiné par son type à contenir une valeur atomique. On oppose valeur atomique à valeur composite. 
    //Un entier, un nombre flottant sont des valeurs atomiques. Un tableau ou une table associative sont des valeurs composites. 
    //Une valeur composite est une structure de données composée récursivement ou non de valeurs scalaires.
    // Une chaîne de caractères peut être considérée comme un tableau ou une valeur scalaire selon le langage de programmation."


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


    public function findAvailableMonths(): array
    {
        return $this->createQueryBuilder('d')
            ->select("DISTINCT SUBSTRING(d.createdAt, 1, 7) AS month") // YYYY-MM
            ->orderBy('month', 'DESC')
            ->getQuery()
            ->getResult();
    }




    // SELECT MONTH(created_at) AS month, SUM(weight) AS totalWeight 
    // FROM donations
    // WHERE YEAR(created_at) = :year (si $year est fourni)
    // GROUP BY MONTH(created_at)
    // ORDER BY month ASC;
    // ex : 
    // month 	totalWeight 	
    // 3 	    672.905
    public function findTotalWeightByMonth($year = null)
    {
        $qb = $this->createQueryBuilder('d')
            ->select('MONTH(d.createdAt) AS month', 'SUM(d.weight) AS totalWeight')
            ->groupBy('month')
            ->orderBy('month', 'ASC');


        if ($year) {
            $qb->andWhere('YEAR(d.createdAt) = :year')
                ->setParameter('year', $year);
        }

        return $qb->getQuery()->getResult();
    }




    // SELECT YEAR(created_at) AS year, SUM(weight) AS totalWeight
    // FROM donations
    // GROUP BY YEAR(created_at)
    // ORDER BY year ASC;

    // ex : 
    // year  	totalWeight 	
    // 2025 	683.24999999

    public function findTotalWeightByYear()
    {
        return $this->createQueryBuilder('d')
            ->select('YEAR(d.createdAt) AS year', 'SUM(d.weight) AS totalWeight')
            ->groupBy('year')
            ->orderBy('year', 'ASC')
            ->getQuery()
            ->getResult();
    }


    // SELECT DATE(created_at) AS day, SUM(weight) AS totalWeight
    // FROM donations
    // GROUP BY day
    // ORDER BY totalWeight DESC
    // LIMIT 1;
    public function getRecordWeightDay()
    {
        $sql = "
            SELECT DATE(d.created_at) AS day, SUM(d.weight) AS totalWeight
            FROM donations d
            GROUP BY day
            ORDER BY totalWeight DESC
            LIMIT 1
        ";

        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAssociative();
    }


    //     SELECT 
    //     DAY(d.created_at) AS day, 
    //     SUM(d.weight) AS totalWeight
    // FROM donations d
    // WHERE YEAR(d.created_at) = 2025
    // AND MONTH(d.created_at) = 02
    // GROUP BY day
    // ORDER BY day ASC;

    public function findTotalWeightByDayForMonth($year, $month)
    {


        return $this->createQueryBuilder('d')
            ->select('DAY(d.createdAt) as day', 'SUM(d.weight) as totalWeight')
            ->where('YEAR(d.createdAt) = :year')
            ->andWhere('MONTH(d.createdAt) = :month')
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->setParameter('year', $year)
            ->setParameter('month', $month)
            ->getQuery()
            ->getResult();
    }



    public function findTotalWeightOfClothingByPeriod($period, $year = null, $month = null): Collection
    {
        $qb = $this->createQueryBuilder('d')
            ->select('SUM(d.weight) as totalWeight')
            ->join('d.category', 'c')
            ->where('c.id = :categoryId')
            ->setParameter('categoryId', 1);

        if ($period === 'monthly') {
            $qb->addSelect('MONTH(d.createdAt) as month')
                ->andWhere('YEAR(d.createdAt) = :year')
                ->setParameter('year', $year)
                ->groupBy('month');
        } elseif ($period === 'yearly') {
            $qb->addSelect('YEAR(d.createdAt) as year')
                ->groupBy('year');
        } elseif ($period === 'daily') {
            $qb->addSelect('DAY(d.createdAt) as day')
                ->andWhere('YEAR(d.createdAt) = :year AND MONTH(d.createdAt) = :month')
                ->setParameters(new ArrayCollection([
                    new Parameter('year', $year),
                    new Parameter('month', $month)
                ]))
                ->groupBy('day');
        }

        $result = $qb->getQuery()->getResult();

        return new ArrayCollection($result);
    }
}


    //    /**
    //     * @return Donation[] Returns an array of Donation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Donation
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
