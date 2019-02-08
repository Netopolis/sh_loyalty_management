<?php

namespace App\Repository;

use App\Entity\LoyaltyCardRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LoyaltyCardRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoyaltyCardRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoyaltyCardRequest[]    findAll()
 * @method LoyaltyCardRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardRequestRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LoyaltyCardRequest::class);
    }


    public function findNewRequest($center_id)
    {
        //
        return $this->createQueryBuilder('c')
            ->join('c.customer', 'm')
            ->andWhere('c.status = :val')
            ->setParameter('val', 0)
            ->andWhere('m.preferredCenter = :val2')
            ->setParameter('val2', $center_id)
            ->orderBy('c.dateOfRequest', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findCardDisposal($center_id)
    {
        //
        return $this->createQueryBuilder('c')
            ->join('c.customer', 'm')
            ->andWhere('c.status = :val3')
            ->setParameter('val3', 1)
            ->andWhere('m.preferredCenter = :val4')
            ->setParameter('val4', $center_id)
            ->orderBy('c.dateOfRequest', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findRequestCard($customer_id)
    {
        //
        return $this->createQueryBuilder('c')
            ->join('c.customer', 'm')
            ->andWhere('c.status = :val3')
            ->setParameter('val3', 0)
            ->andWhere('m.id = :val4')
            ->setParameter('val4', $customer_id)
            ->orderBy('c.dateOfRequest', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?CardRequest
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
