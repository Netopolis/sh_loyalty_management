<?php

namespace App\DataFixtures;

use App\Entity\LoyaltyCard;
use App\Entity\Customer;
use App\Service\QRCodeEncoder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoyaltyCardFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $helper= new AppFixturesHelper();

        // Create 24 loyalty cards for random customers - some properties are set in the constructor (isValid == true)
        $selectedCustomers = array(); // must be declared before the loop, unique customers with a card
        for ($i = 1; $i <= 24; $i++) {
            $card = new LoyaltyCard();
            // deprecated below
            // $customer = $this->getReference('customer' . mt_rand(1, $GLOBALS['count_customers']));
            // this ensures that each customer has only 1 loyalty card
            // we break as soon as the choice is made - we just need one unique customer
            while (true) {
            $rand = mt_rand(1, $GLOBALS['count_customers']);
            if (!in_array($rand, $selectedCustomers)) {
                $selectedCustomers [] = $rand;
                $customer = $this->getReference('customer' . $rand);
                break;
               }
            }
            $center = $customer->getPreferredCenter();
            $centerCode = $center->getCenterCode();
            $customerCode = $customer->getCustomerCode();
            $card->setCardCode($centerCode . $customerCode . (($centerCode+$customerCode)%9));
            // $card->setQRCode($this->generateQRCode($card->getCardCode())); // deprecated very basic, test function
            $QRCodeEncoder = new QRCodeEncoder();
            $customerName = $customer->getFullName();
            $qrCode = $QRCodeEncoder->encodeQRCode($card->getCardCode(), $customerName);
            $card->setQRCode($qrCode);
            // the card's issue date must be >= to the customer registration date
            $registrationDate = $customer->getRegistrationDate();
            $card->setDateOfIssue($helper->generateRandDate($registrationDate, '-1 week'));

            // 65% chance of using the phone app
            if ((mt_rand(1, 100)) <= 65) {
                $card->setIsPhoneAppActive(true);
            }
            $card->setLoyaltyPoints(mt_rand(185,2310));
            $card->setCustomer($customer);
            $card->setCenter($center);
            $card->setStatus('withdraw');
            $manager->persist($card);
        }

        $manager->flush();
    }


    /**
     * Deprecated, old method - now using the QRCodeEncoder service
     * @param string $cardCode
     * @return string
     */
    public function generateQRCode(string $cardCode) {
        $QRCode = 'www.shinigami.com/' . $cardCode;
        return $QRCode;
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
            CustomerFixtures::class
        ];
    }
}
