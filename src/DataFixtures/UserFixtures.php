<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $helper= new AppFixturesHelper();

		// Set up the admins
		$admins = array (
			"Hugues" => array(
				"firstName" => "Hugues",
				"lastName" => "Del",
				"email" => "hugueswf3@gmail.com",
				"password" => "hugues"
			),
			"Cecile" => array(
				"firstName" => "Cecile",
				"lastName" => "Bcj",
				"email" => "cboucajay@gmail.com",
				"password" => "cecile"
			)
		);
		
        // Create Cecile and Hugues with ROLE_ADMIN - password is plain above
		foreach ($admins as $admin) {
			$user = new User();
			$user->setFirstName($admin['firstName']);
			$user->setLastName($admin['lastName']);
			$user->setEmail($admin['email']);
			$user->setPassword(password_hash($admin['password'], PASSWORD_BCRYPT));
			$user->setCenter($this->getReference('center_1'));
            $user->setIsActive(true);
			$user->setRoles(['ROLE_ADMIN']);
			$manager->persist($user);
		}


        // Create 17 users with ROLE_STAFF - login on 'staff' .$i .'@gmail.com', with password == 'staff' .$i
        for ($i = 1; $i <= 17; $i++) {
            $user = new User();
            $user->setFirstName($helper->generateFirstName());
            $user->setLastName($helper->generateLastName());
            $user->setEmail('staff' .$i . '@gmail.com');
            $user->setPassword(password_hash(('staff' .$i), PASSWORD_BCRYPT));
            // select a random center, between 1 and max number
            $user->setCenter($this->getReference('center' . mt_rand(1, $GLOBALS['count_centers'])));
            $user->setIsActive(true);
            $user->setRoles(['ROLE_STAFF']);
            $manager->persist($user);
        }

        $manager->flush();
		
		return true;
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
