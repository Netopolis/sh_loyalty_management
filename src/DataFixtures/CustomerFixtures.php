<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\CustomerActivity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CustomerFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $helper= new AppFixturesHelper();

        // Create 70 customers - ROLE doesn't need to be set, declared in the class constructor
        // login on 'client' .$i .'@gmail.com' - will be password == 'client' .$i
        // if you need more, just change the value in the line below , it will be reused as max value through all the Fixtures
        $GLOBALS['count_customers'] = 85; // number of customers
        for ($i = 1; $i <= $GLOBALS['count_customers']; $i++) {
            $customer = new Customer();
            $location = $helper->generateCity();
            $customer->setFirstName($helper->generateFirstName());
            $customer->setLastName($helper->generateLastName());
            // 68% chance of a nickname
            if ((mt_rand(1, 100)) <= 68) {
                $customer->setNickname($helper->generateNickName());
            }
            $customer->setEmail('client' .$i . '@gmail.com');
            $customer->setPassword(password_hash(('client' .$i), PASSWORD_BCRYPT));
            // 70% chance of phone beginning with "06", else "07"
            $phonePrefix = array(6,7);
            $prefix = $phonePrefix[ mt_rand(1, 100) > 70 ? 1 : 0 ];
            // now the phone itself
            $customer->setPhone('0' . $prefix . mt_rand(11111111,99999999));
            $customer->setAddress($helper->generateAddress());
			$customer->setZipCode($location['zipCode']);
			$customer->setCity($location['city']);
			$customer->setCountry('France');
			$customer->setCustomerCode(100000 + $i);
			// our assumption is that each customer registered between 3.5 years ago and the last month
			$customer->setRegistrationDate($helper->generateRandDate('-42 months', '-1 month'));
			$customer->setBirthDate($helper->generateRandDate('-40 years', '-20 years'));
            // select a random center, between 1 and max number
            $customer->setPreferredCenter($this->getReference('center' . mt_rand(1, $GLOBALS['count_centers'])));
            $this->addReference('customer'.$i, $customer);

            // Needed to initialize CustomerActivity, which will be persisted through cascade
            $activity = $customer->getCustomerActivity();
            $manager->initializeObject($activity);


            $manager->persist($customer);
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
            CenterFixtures::class
        ];
    }
}
