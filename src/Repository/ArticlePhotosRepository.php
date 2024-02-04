<?php

namespace App\Repository;

use App\Entity\ArticlePhotos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ArticlePhotos|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticlePhotos|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticlePhotos[]    findAll()
 * @method ArticlePhotos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticlePhotosRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ArticlePhotos::class);
    }

    // /**
    //  * @return ArticlePhotos[] Returns an array of ArticlePhotos objects
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
    public function findOneBySomeField($value): ?ArticlePhotos
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
