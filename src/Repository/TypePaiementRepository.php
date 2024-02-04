<?php

namespace App\Repository;

use App\Entity\TypePaiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TypePaiement|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypePaiement|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypePaiement[]    findAll()
 * @method TypePaiement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypePaiementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TypePaiement::class);
    }

    // /**
    //  * @return TypePaiement[] Returns an array of TypePaiement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypePaiement
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
