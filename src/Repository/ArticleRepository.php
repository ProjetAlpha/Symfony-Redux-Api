<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Get all published article (e.g. available to users).
     *
     * @return Article[]
     */
    public function getAllPublished()
    {
        return $this->createQueryBuilder('u')
        ->select('u.is_draft, u.id, u.raw_data')
        ->andWhere('u.is_draft = false OR u.is_draft IS NULL')
        ->getQuery()
        ->getResult();
    }

    /**
     * Find a specified administrator article.
     *
     * @param $userId
     * @param $articleId
     *
     * @return Article
     */
    public function findAdminArticle($userId, $articleId)
    {
        return $this->createQueryBuilder('u')
        ->select('u')
        ->andWhere('u.user_id = :user_id')
        ->andWhere('u.id = :article_id')
        ->setParameters(['user_id' => $userId, 'article_id' => $articleId])
        ->getQuery()
        ->getOneOrNullResult();
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
