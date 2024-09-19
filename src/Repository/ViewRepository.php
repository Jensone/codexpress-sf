<?php

namespace App\Repository;

use App\Entity\Note;
use App\Entity\View;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<View>
 */
class ViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, View::class);
    }

    public function findByNote(Note $note): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.note = :note')
            ->setParameter('note', $note)
            ->orderBy('v.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByIpAddress(string $ip_address): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.ip_address = :ip_address')
            ->setParameter('ip_address', $ip_address)
            ->orderBy('v.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findCountByNote(Note $note): int
    {
        return $this->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->where('v.note = :note')
            ->setParameter('note', $note)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findCountByIpAddress(string $ip_address): int
    {
        return $this->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->where('v.ip_address = :ip_address')
            ->setParameter('ip_address', $ip_address)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    //    /**
    //     * @return View[] Returns an array of View objects
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

    //    public function findOneBySomeField($value): ?View
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
