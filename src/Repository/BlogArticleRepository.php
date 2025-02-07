<?php

namespace App\Repository;

use App\DTO\BlogArticleDTO;
use App\Entity\BlogArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlogArticle>
 */
class BlogArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogArticle::class);
    }

    public function findAllDTO(): array
    {
        $blogs = $this->findAll();
        $blogDTOs = [];

        foreach ($blogs as $blog) {
            $blogDTOs[] = new BlogArticleDTO($blog->getId(), $blog->getTitle(), $blog->getSlug(), $blog->getContent(), $blog->getAuthor());
        }

        return $blogDTOs;
    }

    //    /**
    //     * @return BlogArticle[] Returns an array of BlogArticle objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?BlogArticle
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
