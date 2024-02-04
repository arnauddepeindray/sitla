<?php

namespace App\Repository;

use App\Entity\Articles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Articles|null find($id, $lockMode = null, $lockVersion = null)
 * @method Articles|null findOneBy(array $criteria, array $orderBy = null)
 * @method Articles[]    findAll()
 * @method Articles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Articles::class);
    }

    // /**
    //  * @return Articles[] Returns an array of Articles objects
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
    public function findOneBySomeField($value): ?Articles
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findArticleByPage($page, $maxArticles, $category_id, $public){
        $qd = $this->createQueryBuilder('a');
        if($category_id != null){

            if(!$public) {
                $qd
                    ->andWhere('a.category = :category')
                    ->setParameter('category', $category_id)
                    ->setFirstResult(($page - 1) * $maxArticles)
                    ->setMaxResults($maxArticles);
            }
            else{
                $qd
                    ->andWhere('a.category = :category')
                    ->setParameter('category', $category_id)
                    ->andWhere('a.public = 1')
                    ->setFirstResult(($page - 1) * $maxArticles)
                    ->setMaxResults($maxArticles);
            }
        }
        else{
            if(!$public){
                $qd
                    ->setFirstResult(($page - 1) * $maxArticles)
                    ->setMaxResults($maxArticles);
            }
            else{
                $qd
                    ->andWhere('a.public = 1')
                    ->setFirstResult(($page - 1) * $maxArticles)
                    ->setMaxResults($maxArticles);
            }

        }

        return new Paginator($qd);
    }
}
