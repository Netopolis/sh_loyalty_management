<?php

namespace App\Repository;

use App\Entity\LoyaltyCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LoyaltyCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoyaltyCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoyaltyCard[]    findAll()
 * @method LoyaltyCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoyaltyCardRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LoyaltyCard::class);
    }

    /** Displays all valid loyalty cards and their customers - used for the base list (cards index)
     * @return mixed
     */
    public function findAllCardsAndCustomers() {
        return $this->createQueryBuilder('l')
            ->innerJoin('l.customer', 'c')
            ->select('l.id', 'l.cardCode', 'l.QRCode', 'l.loyaltyPoints', 'l.dateOfIssue', 'l.isValid', 'l.isPhoneAppActive', 'l.status', 'c.firstName', 'c.lastName')
            ->addSelect('c.id AS customerId')
            ->orderBy('l.cardCode', 'ASC')
            ->getQuery()
            ->getResult();
    }
	
    /** Displays all valid loyalty cards and their customers - used for the base list (cards index)
     * @return mixed
     */
    public function findAllValidCardsAndCustomers() {
        return $this->createQueryBuilder('l')
            ->innerJoin('l.customer', 'c')
            ->select('l.id', 'l.cardCode', 'l.QRCode', 'l.loyaltyPoints', 'l.dateOfIssue', 'l.isValid', 'l.isPhoneAppActive', 'l.status', 'c.firstName', 'c.lastName')
            ->addSelect('c.id AS customerId')
            ->Where('l.isValid = 1')
            ->orderBy('l.cardCode', 'ASC')
            ->getQuery()
            ->getResult();
    }
    
    /** Displays all invalid loyalty cards and their customers
     * @return mixed
     */
    public function findAllInactiveCardsAndCustomers() {
        return $this->createQueryBuilder('l')
            ->innerJoin('l.customer', 'c')
            ->select('l.id', 'l.cardCode', 'l.QRCode', 'l.loyaltyPoints', 'l.dateOfIssue', 'l.isValid', 'l.isPhoneAppActive', 'l.status', 'c.firstName', 'c.lastName')
            ->addSelect('c.id AS customerId')
            ->Where('l.isValid = 0')
            ->orderBy('l.cardCode', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find a customer by his card code
     * @param $cardCode
     * @return mixed
     */
    public function findCustomerByCardCode($cardCode) {
        try {
            return $this->createQueryBuilder('l')
                ->innerJoin('l.customer', 'c')
                ->addSelect('c')
                ->andWhere('l.cardCode = :cardCode')
                ->setParameter('cardCode', $cardCode)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }

    }

    /**
     * Counts valid loyalty cards for the dashboard
     * @return int|mixed
     */
    public function countValidLoyaltyCards()
    {
        try {
            return $this->createQueryBuilder('l')
                ->select('COUNT(l)')
                ->where('l.isValid = 1')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }


    /**
     * Tallies every loyalty points earned, anytime, for the dashboard
     * Change this if we change in our entities how these points are handled
     */
    public function loyaltyPointsEarnedTotal()
    {
        try {
            return $this->createQueryBuilder('l')
                ->select('SUM(l.loyaltyPoints) as loyaltyPointsTotal')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return $e;
        }
    }

    /**
     * Find validated Cards
     * @return mixed
     */
    public function findValidatedCards() {

        return $this->createQueryBuilder('l')
            ->innerJoin('l.customer', 'c')
            ->addSelect('c')
            ->andWhere('l.status = :val1')
            ->setParameter('val1', 'validated')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find delivered Cards
     * @return mixed
     */
    public function findDeliveredCards() {

        return $this->createQueryBuilder('l')
            ->innerJoin('l.customer', 'c')
            ->addSelect('c')
            ->andWhere('l.status = :val1')
            ->setParameter('val1', 'supplied')
            ->getQuery()
            ->getResult();
    }



    // /**
    //  * @return LoyaltyCard[] Returns an array of LoyaltyCard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LoyaltyCard
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
