<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\VolunteerRegistration;
use App\Entity\VolunteerSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VolunteerRegistration>
 */
class VolunteerRegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VolunteerRegistration::class);
    }


    public function findUpcomingSessionsByUser($user): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.session', 's')
            ->where('r.user = :user')
            ->andWhere('r.status = :status')
            ->andWhere('s.startDatetime >= :today')
            ->setParameter('user', $user)
            ->setParameter('status', 'registered')
            ->setParameter('today', new \DateTimeImmutable('today'))
            ->getQuery()
            ->getResult();
    }

    public function findRegistrationBySessionAndUser(VolunteerSession $session, User $user): ?VolunteerRegistration
    {
        return $this->createQueryBuilder('vr')
            ->andWhere('vr.session = :session')
            ->andWhere('vr.user = :user')
            ->andWhere('vr.status = :status')
            ->setParameter('session', $session)
            ->setParameter('user', $user)
            ->setParameter('status', 'registered')
            ->getQuery()
            ->getOneOrNullResult();
    }






    // public function findRegistrationsByUser($user): array
    // {
    //     return $this->createQueryBuilder('s')
    //         ->innerJoin('s.v', 'r')
    //         ->andWhere('r.user = :user')
    //         ->setParameter('user', $user)
    //         ->getQuery()
    //         ->getResult();
    // }

    //    /**
    //     * @return VolunteerRegistration[] Returns an array of VolunteerRegistration objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?VolunteerRegistration
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
