<?php

namespace App\Tests\Functional\Repository;

use App\Entity\Center;
use App\Entity\User;
use App\Repository\CenterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @method Center|null find($id, $lockMode = null, $lockVersion = null)
 * @method Center|null findOneBy(array $criteria, array $orderBy = null)
 * @method Center[]    findAll()
 * @method Center[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CenterRepositoryTest extends KernelTestCase
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

    public function testGetCenterRepository()
    {
        /** Test constructor
         */
        $repository = $this->createMock(CenterRepository::class);

        $this->assertInstanceOf(CenterRepository::class, $repository);
    }

    /**
     * Get the Max / latest center code, for creating a new one
     */
    public function testGetMaxCenterCode()
    {
        $lastCode = $this->entityManager
            ->getRepository(Center::class)
            ->getMaxCenterCode()
        ;

        $this->assertArrayHasKey('lastCode', $lastCode);
        $this->assertTrue($lastCode['lastCode'] > 100);

    }

    /**
     * Counts current centers for the dashboard
     */
    public function testCountCenters()
    {
        $centersCount = $this->entityManager
            ->getRepository(Center::class)
            ->countCenters()
        ;
        $centersCount = (int)$centersCount;

        $this->assertTrue($centersCount > 0);
        $this->assertTrue(is_int($centersCount));
    }

    /**
     * Finds users working at each center (staff)
     */
    public function testFindCentersAndStaff()
    {
        $results = $this->entityManager
            ->getRepository(Center::class)
            ->findCentersAndStaff()
        ;

        $centers = new ArrayCollection($results);

        if (!empty($centers)) {
            foreach ($centers as $center) {
                if (!empty($center->getUsers())) {
                    $this->assertNotEmpty($center->getUsers());
                } else {
                    $this->assertEmpty($center->getUsers());
                }
            }
        } else {
            $this->assertEmpty($centers);
        }
    }

    public function testFindPublishedCenters()
    {
        $result = $this->entityManager
            ->getRepository(Center::class)
            ->findPublishedCenters()
        ;

        $publishedCenters = new ArrayCollection($result);

        if (!empty($publishedCenters)) {
            foreach ($publishedCenters as $center) {
                $this->assertTrue($center->getPublished());
            }
        } else {
            $this->assertEmpty($publishedCenters);
        }
    }
    
    // /**
    //  * @return Center[] Returns an array of Center objects
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
