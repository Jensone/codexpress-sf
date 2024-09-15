<?php

namespace App\Repository;

use App\Entity\Note;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Note>
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    // Recherche par mot-clé dans le tite et le contenu de note publiques
    public function findBySearch(string $query): array
    {
        $qb = $this->createQueryBuilder('n');
        $qb
            ->where('n.is_public = true') // Filtre les notes publiques
            ->andWhere('n.title LIKE :query OR n.content LIKE :query') // Recherche par mot-clé
            ->setParameter('query', '%' . $query . '%') // Ajoute le mot-clé à la requête
            ->orderBy('n.created_at', 'DESC') // Trie par date de création
            ->setMaxResults(100) // Limite à 100 résultats
            ;
        return $qb->getQuery()->getResult();
    }
    
    // 3 dernières publiques créées par l'utilisateur
    public function findByCreator(User $user): array
    {
        $qb = $this->createQueryBuilder('n');
        $qb
            ->where('n.creator = :user') // Filtre les notes créées par l'utilisateur
            ->andWhere('n.is_public = true') // Filtre les notes publiques
            ->setParameter('user', $user)
            ->orderBy('n.created_at', 'DESC') // Trie par date de création
            ->setMaxResults(3) // Limite à 3 résultats
            ;
        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Note[] Returns an array of Note objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Note
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
