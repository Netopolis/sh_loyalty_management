<?php

namespace App\Tests\Functional\Repository;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Center;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testGetCustomerRepository()
    {
        /** Test constructor
         */
        $customerRepository = $this->createMock(CustomerRepository::class);

        $this->assertInstanceOf(CustomerRepository::class, $customerRepository);
    }

    /**
     * Find all active customers, ordered by last name
     */
    public function testFindAllActiveCustomers()
    {
        $result = $this->entityManager
            ->getRepository(Customer::class)
            ->findAllActiveCustomers()
        ;

        $activeCustomers = new ArrayCollection($result);

        if (!empty($activeCustomers)) {
            foreach ($activeCustomers as $customer) {
                $this->assertTrue($customer->getIsActive());
            }
        } else {
            $this->assertEmpty($activeCustomers);
        }
    }

    /**
     * Find all inactive customers, ordered by last name
     */
    public function testFindAllInactiveCustomers()
    {
        $inactiveCustomers = $this->entityManager
            ->getRepository(Customer::class)
            ->findAllInactiveCustomers()
        ;

        if (!empty($inactiveCustomers)) {
            foreach ($inactiveCustomers as $customer) {
                $this->assertFalse($customer['isActive']);
            }
        } else {
            $this->assertEmpty($inactiveCustomers);
        }
    }

    /**
     * Find all active customers and their activities
     */
    public function testFindActiveCustomersActivities() {
        $custActivities = $this->entityManager
            ->getRepository(Customer::class)
            ->findActiveCustomersActivities()
        ;

        foreach ($custActivities as $activity) {
            $this->assertNotEmpty($activity['id']);
            $this->assertNotEmpty($activity['lastName']);
            $this->assertNotEmpty($activity['gamesPlayed']);
        }
    }

    /**
     * Find the customers of a specific center / club
     */
    public function testFindCustomersByCenter()
    {
        $result = $this->entityManager
            ->getRepository(Center::class)
            ->findPublishedCenters()
        ;

        $centers = new ArrayCollection($result);
        $center = $centers [0];
        $centerId = $center->getId();

        $customersOfCenter = $this->entityManager
            ->getRepository(Customer::class)
            ->findCustomersByCenter($centerId)
        ;

        $customers = new ArrayCollection($customersOfCenter);

        if (!empty($customers)) {
            foreach ($customers as $customer) {
                $this->assertNotEmpty($customer->getLastName());
                $this->assertTrue($customer->getIsActive());
            }
        } else {
            $this->assertEmpty($customers);
        }
    }

    /**
     * Get the Max (latest) customer code, when creating a new customer
     */
    public function testGetMaxCustomerCode()
    {
        $lastCode = $this->entityManager
            ->getRepository(Customer::class)
            ->getMaxCustomerCode()
        ;

        $this->assertArrayHasKey('lastCode', $lastCode);
        $this->assertTrue($lastCode['lastCode'] > 10000);
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
                ->Where('c.lastName LIKE :term OR c.firstName LIKE :term')
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
     * Counts active customers for the dashboard
     */
    public function testCountActiveCustomers()
    {
        $customersCount = $this->entityManager
            ->getRepository(Customer::class)
            ->countActiveCustomers()
        ;

        $customersCount = (int)$customersCount;

        $this->assertTrue($customersCount > 0);
        $this->assertTrue(is_int($customersCount));
    }

//    /**
//     * Selects customers without Loyalty Card nor card request
//     */
//    public function testCustomersWithoutCard()
//    {
//        $customers = $this->entityManager
//            ->getRepository(Customer::class)
//            ->CustomersWithoutCard()
//        ;
//
//        // $customers = new ArrayCollection($customersWithoutCard);
//        dd($customers);
//
//        if (!empty($ustomers)) {
//            foreach ($ustomers as $customer) {
//                $this->assertTrue($customer->getIsActive());
//                $this->assertEmpty($customer->getCards());
//                $this->assertEmpty($customer->getCardRequest());
//            }
//        } else {
//            $this->assertEmpty($customers);
//        }
//    }


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

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}
