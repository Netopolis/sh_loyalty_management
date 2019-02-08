<?php

namespace App\Tests\Functional\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepositoryTest extends KernelTestCase
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

    public function testGetUserRepository()
    {
        /** Test constructor
         */
        $repository = $this->createMock(UserRepository::class);

        $this->assertInstanceOf(UserRepository::class, $repository);
    }

    /**
     * Find all active users, ordered by last name
     */
    public function testFindAllActiveUsers()
    {
        $activeUsers = $this->entityManager
            ->getRepository(User::class)
            ->findAllActiveUsers()
        ;

        foreach ($activeUsers as $user) {
            $this->assertTrue($user['isActive']);
        }
    }

    /**
     * Find all inactive users, ordered by last name
     */
    public function testFindAllInactiveUsers()
    {
        $result = $this->entityManager
            ->getRepository(User::class)
            ->findAllInactiveUsers()
        ;

        $inactiveUsers = new ArrayCollection($result);

        if (!empty($inactiveUsers)) {
            foreach ($inactiveUsers as $user) {
                $this->assertFalse($user->getIsActive());
            }
        } else {
            $this->assertEmpty($inactiveUsers);
        }
    }

    /**
     * Find users with role admin, not assigned to any center
     */
    public function testFindUnassignedAdmins()
    {
        $admins = $this->entityManager
            ->getRepository(User::class)
            ->findUnassignedAdmins()
        ;

        if (!empty($admins)) {
            foreach ($admins as $admin) {
                $this->assertNotEmpty($admin->getRoles());
                $this->assertContains('ROLE_ADMIN', $admin->getRoles());
            }
        } else {
            $this->assertEmpty($admins);
        }
    }

    /**
     * Counts active staff members (with ROLE_STAFF), for the dashboard
     */
    public function testCountActiveStaff()
    {
        $staffCount = $this->entityManager
            ->getRepository(User::class)
            ->CountActiveStaff()
        ;

        $activeUsers = $this->entityManager
            ->getRepository(User::class)
            ->findAllActiveUsers()
        ;

        $admins = $this->entityManager
            ->getRepository(User::class)
            ->findUnassignedAdmins()
        ;

        $totalActiveUsers = count($activeUsers);
        $totalAdmins = count($admins);

        $this->assertEquals($staffCount, ($totalActiveUsers - $totalAdmins));
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
