<?php

namespace App\Tests\Functional\Repository;

use App\Entity\Center;
use App\Entity\LoyaltyCardRequest;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @method LoyaltyCardRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoyaltyCardRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoyaltyCardRequest[]    findAll()
 * @method LoyaltyCardRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardRequestRepositoryTest extends KernelTestCase
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

    /**
    * Returns an array of CardRequest objects
    */
    public function testFindNewRequest()
    {
        $result = $this->entityManager
            ->getRepository(Center::class)
            ->findPublishedCenters()
        ;

        $centers = new ArrayCollection($result);
        $center = $centers [0];
        $centerId = $center->getId();

        $cardRequestsAtCenter = $this->entityManager
            ->getRepository(LoyaltyCardRequest::class)
            ->findNewRequest($centerId)
        ;

        $requests = new ArrayCollection($cardRequestsAtCenter);

        if (!empty($requests)) {
            foreach ($requests as $request) {
                $this->assertSame(0, $request->getStatus());
            }
        } else {
            $this->assertEmpty($requests);
        }
    }

    public function testFindCardDisposal()
    {
        $result = $this->entityManager
            ->getRepository(Center::class)
            ->findPublishedCenters()
        ;

        $centers = new ArrayCollection($result);
        $center = $centers [0];
        $centerId = $center->getId();

        $cardDisposalsAtCenter = $this->entityManager
            ->getRepository(LoyaltyCardRequest::class)
            ->findCardDisposal($centerId)
        ;

        $requests = new ArrayCollection($cardDisposalsAtCenter);

        if (!empty($requests)) {
            foreach ($requests as $request) {
                $this->assertSame(1, $request->getStatus());
            }
        } else {
            $this->assertEmpty($requests);
        }
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
