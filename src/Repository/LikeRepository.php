<?php

namespace App\Repository;

use App\Entity\Like;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Like>
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function findByNote(string $note): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.note = :note')
            ->setParameter('note', $note)
            ->orderBy('l.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByIdAndCreator(int $id, User $creator): ?Like
    {
        return $this->createQueryBuilder('l')
            ->where('l.note = :id')
            ->andWhere('l.creator = :creator')
            ->setParameter('id', $id)
            ->setParameter('creator', $creator)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //    /**
    //     * @return Like[] Returns an array of Like objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Like
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
