<?php

namespace App\Repository;

use App\Entity\OutgoingWeighing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OutgoingWeighing>
 */
class OutgoingWeighingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OutgoingWeighing::class);
    }

    //exemple SQL for monthly data:

    // SELECT 
    // MONTH(created_at) AS month, 
    // SUM(weight) AS totalData
    // FROM outgoing_weighings
    // WHERE YEAR(created_at) = 2025
    // GROUP BY MONTH(created_at)
    // ORDER BY MONTH(created_at) ASC;

    public function findTotalDataByMonth(?string $year = null): array
    {
        $qb = $this->createQueryBuilder('o')
            ->select('MONTH(o.createdAt) AS month', 'SUM(o.weight) AS totalData')
            ->groupBy('month')
            ->orderBy('month', 'ASC');

        if ($year) {
            $qb->andWhere('YEAR(o.createdAt) = :year')
                ->setParameter('year', $year);
        }

        return $qb->getQuery()->getResult();
    }

    //exemple SQL for yearly data:  
    //  SELECT 
    //  YEAR(created_at) AS year, 
    //   SUM(weight) AS totalData
    // FROM outgoing_weighings
    // GROUP BY YEAR(created_at)
    // ORDER BY YEAR(created_at) ASC;


    public function findTotalDataByYear(): array
    {
        $qb = $this->createQueryBuilder('o')
            ->select('YEAR(o.createdAt) AS year', 'SUM(o.weight) AS totalData')
            ->groupBy('year')
            ->orderBy('year', 'ASC');

        return $qb->getQuery()->getResult();
    }


    public function findTotalDataByDayForMonth(?string $year = null, ?string $month = null): array
    {
        $qb = $this->createQueryBuilder('o')
            ->select('DAY(o.createdAt) AS day', 'SUM(o.weight) AS totalData')
            ->groupBy('day')
            ->orderBy('day', 'ASC');

        if ($year) {
            $qb->andWhere('YEAR(o.createdAt) = :year')
                ->setParameter('year', $year);
        }

        if ($month) {
            $qb->andWhere('MONTH(o.createdAt) = :month')
                ->setParameter('month', $month);
        }

        return $qb->getQuery()->getResult();
    }







    //    /**
    //     * @return OutgoingWeighing[] Returns an array of OutgoingWeighing objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?OutgoingWeighing
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
