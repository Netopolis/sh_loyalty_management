<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    // private $em;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Customer::class);
    }


    /**
     * Find all active customers, ordered by last name
     * @return mixed
     */
    public function findAllActiveCustomers()
    {
        return $this->createQueryBuilder('c')
            ->where('c.isActive = 1')
            ->orderBy('c.lastName', 'ASC')
            ->addOrderBy('c.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all inactive customers, ordered by last name
     * @return mixed
     */
    public function findAllInactiveCustomers()
    {
        return $this->createQueryBuilder('c')
            ->where('c.isActive = 0')
            ->join('c.customerActivity', 'ca')
            ->select('c.id', 'c.customerCode', 'c.firstName', 'c.lastName', 'c.nickname', 'c.isActive', 'c.email', 'c.phone', 'c.address', 'c.zipCode', 'c.city', 'c.registrationDate', 'ca.lastActivity')
            ->orderBy('c.lastName', 'ASC')
            ->addOrderBy('c.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all active customers and their activities
     */
    public function findActiveCustomersActivities() {
        return $this->createQueryBuilder('c')
            ->join('c.customerActivity', 'ca')
            //->leftJoin('c.cards', 'l', 'WITH', 'c.id = l.customer', 'AND', 'l.isValid = 1')
            ->leftJoin('c.cards', 'l', 'WHERE l.isValid = 1')
            ->select('c.id', 'c.firstName', 'c.lastName', 'l.id AS cardId', 'l.cardCode', 'l.loyaltyPoints', 'c.registrationDate', 'ca.lastActivity', 'ca.totalSpendingAllTime', 'ca.averageSpendingPerMonth', 'ca.gamesPlayed', 'ca.averageActivitiesPerMonth', 'ca.friendsInvitedToGames', 'ca.customersSponsored')
            ->where('c.isActive = 1')
            ->orderBy('c.lastName', 'ASC')
            ->addOrderBy('c.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find the customers of a specific center / club
     * @param $centerId
     * @return mixed
     */
    public function findCustomersByCenter($centerId)
    {
        return $this->createQueryBuilder('c')
            // All customers of a specific center
            ->where('c.preferredCenter = :center_id')
            ->setParameter('center_id', $centerId)
            // But only active customers
            ->andWhere('c.isActive = 1')
            ->orderBy('c.lastName', 'ASC')
            ->addOrderBy('c.firstName', 'ASC')
            // Let's shoot
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the Max (latest) customer code, when creating a new customer
     * then increment the code by one in the controller
     */
    public function getMaxCustomerCode()
    {
        try {
            return $this->createQueryBuilder('c')
                ->select('MAX(c.customerCode) as lastCode')
                ->getQuery()
                ->getSingleResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        } catch (NoResultException $e) {
            return 0;
        }

    }

    /**
     * Find customers or loyalty cards by typing a string - used by the searchBar
     * The string can be part of their last names, first names, or partial numbers of a card code
     * @param string|null $term
     * @return mixed
     */
    public function findBySearchCriteria(?string $term)
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->leftJoin('c.cards', 'l');

        if ($term) {
            $queryBuilder
                //->andWhere('c.lastName LIKE :term OR c.firstName LIKE :term OR l.cardCode LIKE :term')
                ->where('c.lastName LIKE :term OR c.firstName LIKE :term')
                ->orWhere('l.cardCode LIKE :term')
                ->setParameter('term', '%' . $term . '%');
        }

        return $queryBuilder
            ->orderBy('c.lastName', 'ASC')
            ->addOrderBy('c.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Count active customers for the dashboard
     * @return int|mixed
     */
    public function countActiveCustomers()
    {
        try {
            return $this->createQueryBuilder('c')
                ->select('COUNT(c)')
                ->where('c.isActive = 1')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    /**
     * Select customers without Loyalty Card nor card request
     * @return int|mixed
     */
    public function CustomersWithoutCard()
    {

        // SELECT * FROM sgl_customers WHERE id NOT IN (SELECT customer_id FROM sgl_loyalty_cards WHERE is_valid = 1
        // UNION SELECT customer_id FROM sgl_loyalty_cards_requests WHERE status = 0) AND is_active = 1

        return $this->createQueryBuilder('c')
            ->leftJoin('c.cards', 'ca')
            ->leftJoin('c.loyaltyCardRequest', 'cr')
            ->leftJoin('c.customerActivity', 'cac')
            ->addSelect('ca')
            ->addSelect('cr')
            ->addSelect('cac')
            ->where('((ca IS NULL or ca.isValid != 1) AND (cr.status != 0 OR cr.status IS NULL)) AND c.isActive = 1')
            ->orderBy('c.lastName', 'ASC')
            ->addOrderBy('c.firstName', 'ASC');

    }


    // /**
    //  * @return Customer[] Returns an array of Customer objects
    //  */
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
    public function findOneBySomeField($value): ?Customer
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
