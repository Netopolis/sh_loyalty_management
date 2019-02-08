<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }


    /**
     * Find all active users, ordered by last name
     * @return mixed
     */
    public function findAllActiveUsers()
    {
        return $this->createQueryBuilder('u')
            ->where('u.isActive = 1')
            ->join ('u.center', 'c')
            ->select('u.id', 'u.firstName', 'u.lastName', 'u.email', 'c.city AS center', 'u.isActive', 'u.roles')
            ->orderBy('u.id', 'ASC')
            ->addOrderBy('u.lastName', 'ASC')
            ->addOrderBy('u.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }
	
	    /**
     * Find all active users, ordered by last name
     * @return mixed
     */
    public function findAllUsers()
    {
        return $this->createQueryBuilder('u')
            ->join ('u.center', 'c')
            ->select('u.id', 'u.firstName', 'u.lastName', 'u.email', 'c.city AS center', 'u.isActive', 'u.roles')
            ->orderBy('u.id', 'ASC')
            ->addOrderBy('u.lastName', 'ASC')
            ->addOrderBy('u.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Find all inactive users, ordered by last name
     * @return mixed
     */
    public function findAllInactiveUsers()
    {
        return $this->createQueryBuilder('u')
            ->where('u.isActive = 0')
            ->orderBy('u.lastName', 'ASC')
            ->addOrderBy('u.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Find users with role admin, not assigned to any center
     * @return mixed
     */
    public function findUnassignedAdmins()
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', "%ROLE_ADMIN%")
            //->andWhere('u.center is NULL') // this has been modified
            ->getQuery()
            ->getResult();
    }


    /**
     * Counts active staff members (with ROLE_STAFF), for the dashboard
     *
     */
    public function countActiveStaff()
    {
        try {
            return $this->createQueryBuilder('u')
                ->select('COUNT(u)')
                ->where('u.isActive = 1')
                ->andWhere('u.roles LIKE :roles')
                ->setParameter('roles', "%ROLE_STAFF%")
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    
    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
}
