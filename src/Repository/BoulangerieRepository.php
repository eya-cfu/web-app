<?php

namespace App\Repository;

use App\Entity\Boulangerie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Boulangerie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Boulangerie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Boulangerie[]    findAll()
 * @method Boulangerie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoulangerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Boulangerie::class);
    }

    // /**
    //  * @return Boulangerie[] Returns an array of Boulangerie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Boulangerie
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
