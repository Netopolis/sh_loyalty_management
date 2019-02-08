<?php
/**
 * Created by Hugues
 * 08/01/2019
 */

namespace App\DataFixtures;

use App\Entity\LoyaltyCard;
use App\Entity\Customer;
use App\Entity\LoyaltyCardRequest;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoyaltyCardRequestFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $helper= new AppFixturesHelper();

        // Generate 7 loyalty card requests, for random customers and centers
        $requestingCustomers = array(); // unique customers who do not have a card and request one
        for ($i = 1; $i <= 7; $i++) {
            $cardRequest = new LoyaltyCardRequest();
            while (true) {
                // get a random customer
                $rand = mt_rand(1, $GLOBALS['count_customers']);
                // check if he has not already requested a loyalty card, then if he doesn't have one
                if (!in_array($rand, $requestingCustomers)) {
                    $customer = $this->getReference('customer' . $rand);
                    if (count($customer->getCards()) == 0) {
                        $requestingCustomers [] = $rand; // We have a deal !
                        break;
                    }
                }
            }
            $cardRequest->setCustomer($customer);
            $cardRequest->setDateOfRequest($helper->generateRandDate('-4 days', '-1 day'));
            $cardRequest->setStatus(0);
            $manager->persist($cardRequest);
        }

        $manager->flush();
    }

    
    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            CenterFixtures::class,
            CustomerFixtures::class,
            LoyaltyCardFixtures::class
        ];
    }
}