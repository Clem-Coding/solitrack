<?php

namespace App\Repository;

use App\Entity\Donation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
    public function findTotalWeightDonationsByMonth($year = null)
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


    // // SELECT DATE(created_at) AS date, SUM(weight) AS totalWeight
    // // FROM donations
    // // WHERE DATE(created_at) = ?;
    // public function findTotalWeightDonationsByDay($date)
    // {
    //     // Vérifier si $date est une chaîne de caractères
    //     if (is_string($date)) {
    //         $date = new \DateTime($date);
    //     }

    //     // Vérifier si c'est maintenant un objet DateTime
    //     if ($date instanceof \DateTime) {
    //         // Convertir la date en format 'Y-m-d' (par exemple '2025-02-12')
    //         $startDate = $date->format('Y-m-d') . ' 00:00:00';
    //         $endDate = $date->format('Y-m-d') . ' 23:59:59';
    //     } else {
    //         // Si ce n'est pas un objet DateTime, retourner une erreur ou effectuer une autre logique
    //         throw new \InvalidArgumentException("La date fournie n'est pas un objet DateTime valide.");
    //     }

    //     // Utiliser QueryBuilder pour calculer la somme du poids total sur la journée
    //     return $this->createQueryBuilder('d')
    //         ->select('SUM(d.weight) AS totalWeight') // Sum des poids
    //         ->where('d.createdAt >= :startDate')
    //         ->andWhere('d.createdAt <= :endDate')
    //         ->setParameter('startDate', $startDate)
    //         ->setParameter('endDate', $endDate)
    //         ->getQuery()
    //         ->getSingleResult(); // Utiliser getSingleResult() pour obtenir une seule ligne avec le total
    // }



    // SELECT YEAR(created_at) AS year, SUM(weight) AS totalWeight
    // FROM donations
    // GROUP BY YEAR(created_at)
    // ORDER BY year ASC;

    // ex : 
    // year  	totalWeight 	
    // 2025 	683.24999999

    public function findTotalWeightDonationsByYear()
    {
        return $this->createQueryBuilder('d')
            ->select('YEAR(d.createdAt) AS year', 'SUM(d.weight) AS totalWeight')
            ->groupBy('year')
            ->orderBy('year', 'ASC')
            ->getQuery()
            ->getResult();
    }



    // SELECT DAY(created_at) AS day, SUM(weight) AS totalWeight
    // FROM donations
    // WHERE DATE_FORMAT(created_at, '%Y-%m') = '2023-06' 
    // GROUP BY DAY(created_at)
    // ORDER BY day ASC;

    public function getTotalWeightByDayForMonth($month)
    {
        // Crée un QueryBuilder avec l'alias 'd' pour la table Donation
        $queryBuilder = $this->createQueryBuilder('d');

        $queryBuilder
            ->select('DAY(d.createdAt) as day', 'SUM(d.weight) as totalWeight')
            ->where('DATE_FORMAT(d.createdAt, \'%Y-%m\') = :month')
            ->groupBy('day')
            ->setParameter('month', $month)
            ->orderBy('day', 'ASC');

        return $queryBuilder->getQuery()->getResult();
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
}
