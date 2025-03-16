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





    // SELECT MONTH(created_at) AS month, SUM(weight) AS totalWeight FROM donations GROUP BY MONTH(created_at) ORDER BY month ASC;
    // ex : 
    // month 	totalWeight 	
    // 3 	    579.7549999999999
    public function findTotalDonationsByMonth()
    {
        return $this->createQueryBuilder('d')
            ->select('MONTH(d.createdAt) AS month', 'SUM(d.weight) AS totalWeight')
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
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
