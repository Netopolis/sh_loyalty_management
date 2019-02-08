<?php

namespace App\Tests\Functional\Repository;

use App\Entity\LoyaltyCard;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @method LoyaltyCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoyaltyCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoyaltyCard[]    findAll()
 * @method LoyaltyCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoyaltyCardRepositoryTest extends KernelTestCase
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


    /** Displays all valid loyalty cards and their customers
     */
    public function testFindAllValidCardsAndCustomers()
    {
        $validCards = $this->entityManager
            ->getRepository(LoyaltyCard::class)
            ->findAllValidCardsAndCustomers()
        ;

        foreach ($validCards as $validCard) {
            $this->assertTrue($validCard['isValid']);
            $this->assertNotEmpty($validCard['customerId']);
        }
    }

    /** Displays all invalid loyalty cards and their customers
     */
    public function testFindAllInactiveCardsAndCustomers()
    {
        $invalidCards = $this->entityManager
            ->getRepository(LoyaltyCard::class)
            ->findAllInactiveCardsAndCustomers()
        ;

        if (!empty($invalidCards)) {
        foreach ($invalidCards as $invalidCard) {
            $this->assertFalse($invalidCard['isValid']);
            $this->assertNotEmpty($invalidCard['customerId']);
        }
        } else {
            $this->assertEmpty($invalidCards);
        }
    }

    /**
     * Find a customer by his card code
     */
    public function testFindCustomerByCardCode() {

        $result = $this->entityManager
            ->getRepository(LoyaltyCard::class)
            ->findAllValidCardsAndCustomers()
        ;

        $cards = new ArrayCollection($result);
        $card = $cards [0];
        $cardId = $card['id'];

        $customer = $this->entityManager
            ->getRepository(LoyaltyCard::class)
            ->findCustomerByCardCode($cardId)
        ;

        if (!empty($customer)) {
                $this->assertNotEmpty($customer->getLastName());
                $this->assertNotEmpty($customer->getId());
            } else {
            $this->assertEmpty($customer);
        }
    }

    /**
     * Counts valid loyalty cards for the dashboard
     */
    public function testCountValidLoyaltyCards()
    {
        $cardsCount = $this->entityManager
            ->getRepository(LoyaltyCard::class)
            ->countValidLoyaltyCards()
        ;

        $cardsCount = (int)$cardsCount;

        $this->assertTrue($cardsCount > 0);
        $this->assertTrue(is_int($cardsCount));
    }

    /**
     * Tallies every loyalty points earned, anytime, for the dashboard
     */
    public function testLoyaltyPointsEarnedTotal()
    {
        $lpTotal = $this->entityManager
            ->getRepository(LoyaltyCard::class)
            ->loyaltyPointsEarnedTotal()
        ;

        $lpTotal = (int)$lpTotal;

        $this->assertTrue($lpTotal > 0);
        $this->assertTrue(is_int($lpTotal));
    }

    /**
     * Find validated Cards
     */
    public function testFindValidatedCards() {

        $validatedCards = $this->entityManager
            ->getRepository(LoyaltyCard::class)
            ->findValidatedCards()
        ;

        if (!empty($validatedCards)) {
            foreach ($validatedCards as $card) {
                $this->assertSame('validated', $card->getStatus());
            }
        } else {
            $this->assertEmpty($validatedCards);
        }
    }

    /**
     * Find delivered Cards
     */
    public function testFindDeliveredCards() {

        $deliveredCards = $this->entityManager
            ->getRepository(LoyaltyCard::class)
            ->findDeliveredCards()
        ;

        if (!empty($deliveredCards)) {
            foreach ($deliveredCards as $card) {
                $this->assertSame('supplied', $card->getStatus());
            }
        } else {
            $this->assertEmpty($deliveredCards);
        }
    }

/*      Use the above function in the controller with this, when you get the card code
        $card = $this->getDoctrine()
            ->getRepository(LoyaltyCard::class)
            ->findCustomerByCardCode($cardCode);
        // Then just extract the customer
        $customer = $card->getCustomer();
        // and pass the customer as an argument in the response, eg:
        [
            // other stuff...,
            'customer' => $customer
        ]);
        // Done. 
*/

/*    // Commented out, as it returned the customer AND the card - too clumsy
      public function findCustomerByCard($cardId) {
        return $this->createQueryBuilder('l')
            ->innerJoin('l.customer', 'c')
            ->where('l.id = :card_id')
            ->setParameter('card_id', $cardId)
            ->getQuery()
            ->getResult();
            // We don't even need an order by for this. There won't be that many cards for any customer, and the topic of how many cards per customer is still up to debate.
            // There should be no more than 2 cards per customers (depending on type, smartphone app or plastic) and if you become a VIP, most major companies simply upgrade your card...
    }*/


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
