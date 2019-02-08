<?php

namespace App\Tests\Functional\Repository;

use App\Entity\CustomerActivity;
use App\Repository\CustomerActivityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @method CustomerActivity|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerActivity|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerActivity[]    findAll()
 * @method CustomerActivity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerActivityRepositoryTest extends KernelTestCase
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

    /** Test constructor - that's all we have in this class
     */
    public function testGetCustomerActivityRepository()
    {

        /** Test constructor
         */
        $customerActivityRepository = $this->createMock(CustomerActivityRepository::class);

        $this->assertInstanceOf(CustomerActivityRepository::class, $customerActivityRepository);

    }

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
