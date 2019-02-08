<?php

namespace App\Repository;

use App\Entity\Center;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Center|null find($id, $lockMode = null, $lockVersion = null)
 * @method Center|null findOneBy(array $criteria, array $orderBy = null)
 * @method Center[]    findAll()
 * @method Center[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CenterRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Center::class);
    }


    /**
     * Get the Max / latest center code, for creating a new one
     * and increment it by one in the controller
     */
    public function getMaxCenterCode()
    {
        try {
            return $this->createQueryBuilder('c')
                ->select('MAX(c.centerCode) as lastCode')
                ->getQuery()
                ->getSingleResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }

    }


    /**
     * Counts current centers for the dashboard
     * @return int|mixed
     */
    public function countCenters()
    {
        try {
            return $this->createQueryBuilder('c')
                ->select('COUNT(c)')
                ->where('c.published = 1') // only published ones, not the special admin H.Q.
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }


    /**
     * Finds users working at each center (staff)
     * Achieves the desired results. Other methods below are deprecated
     * @return mixed
     */
    public function findCentersAndStaff()
    {
        return $this->createQueryBuilder('c')
            //->select('c.id AS centerId', 'c.name')
            ->leftJoin('c.users', 'u')
            ->where('c.id = u.center AND u.isActive = 1')
            //->andWhere('u.isActive = 1')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** Called by the function below
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createPublishedCenterQueryBuilder()
    {
        return $this->createQueryBuilder('c')
            //->select('c.id AS centerId', 'c.name')
            ->where('c.published = 1')
            //->andWhere('u.isActive = 1')
            ->orderBy('c.name', 'ASC');
    }

    /** This is the one that should be used
     */
    public function findPublishedCenters()
    {
        return $this->createPublishedCenterQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Center
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
