<?php

namespace App\Repository;

use App\Entity\OrderedDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderedDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderedDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderedDetail[]    findAll()
 * @method OrderedDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderedDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderedDetail::class);
    }

    // /**
    //  * @return OrderedDetail[] Returns an array of OrderedDetail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderedDetail
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
