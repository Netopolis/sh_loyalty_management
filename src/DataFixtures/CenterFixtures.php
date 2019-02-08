<?php

namespace App\DataFixtures;

use App\Entity\Center;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CenterFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $helper= new AppFixturesHelper();

        // Create 6 centers - if you need more, just change the value in the line below , it will cascade as max value through all the Fixtures
        $GLOBALS['count_centers'] = 6; // our number of centers
        $randImages = array(1, 2, 3, 4, 5); // one random image for each center
        $centersLocations = array(); // this array will store locations,  where we already have a center
        $selectedImages = array();
        for ($i = 1; $i <= $GLOBALS['count_centers']; $i++) {
            $center = new Center();
            // main loop starts, in which we avoid duplicate locations - save for the biggest city
            $dijon2 = false;
            while (true) {
                // get a random location
                $location = $helper->generateCity();
                // special check for Dijon, how many times do we have it ?
                if ($location['city'] == 'Dijon') {
                    $count = 0;
                    foreach ($centersLocations as $key => $value) {
                        if ($value['city'] == 'Dijon') {
                            $count++;
                        }
                    }
                    if ($count == 0) {
                        $centersLocations [] = $location; // Dijon, first time
                        break;
                    } elseif ($count == 1) {
                        $dijon2 = true;
                        // we allow a second center at Dijon because it's such a big city
                        $centersLocations [] = $location; // second center at Dijon set
                        break;
                    } elseif ($count > 2) {
                        continue;
                    }
                } elseif (!in_array($location, $centersLocations)) { // check if the location is already in use
                    $centersLocations [] = $location; // If not, we can use it
                    break;
                }
            }
            // set center name
            if ($dijon2) { // second center at Dijon
                $center->setName('Shinigami Laser '.$location['city'] . ' 2');
            } else {
                $center->setName('Shinigami Laser '.$location['city']);
            }
            // 70% chance of fixed line phone, else an internet box
            $phonePrefix = array(3,9);
            $prefix = $phonePrefix[ mt_rand(1, 100) > 70 ? 1 : 0 ];
            $center->setPhone('0' . $prefix . mt_rand(11111111,99999999));
            // format email, based on city name
            $arr = explode("-", $location['city'], 2); // explode long city names with "-"
            if ($arr[0] == 'Saint') { // special case of 'saint-something', we also need the saint's name
                $first = $arr[0] . '-' . $arr[1];
            } else {
                $first = $arr[0]; // get only the first part of a city name
            }
            $first = $helper->stripAccents($first); // remove accents
            $first = str_replace(' ', '', $first); // remove possible white space
            $center->setEmail('club-' . strtolower($first) . '@shinigami.com'); // set to lower case
            $center->setAddress($helper->generateAddress());
			$center->setZipCode($location['zipCode']);
            $center->setCity($location['city']);
            $center->setCountry('France');
            $center->setCenterCode(110 + $i);
            $center->setPublished(1);
            // deprecated version, will not work if number of centers > 5
            // $center->setImageCenter('shinigami-laser_' . $i . '.jpg');
            while (true) {
                // get a random image number
                $rand = mt_rand(1, count($randImages));
                // check if the image is already in use
                if (!in_array($rand, $selectedImages) && $i <= count($randImages)) {
                    $center->setCenterImage('shinigami-laser_' . $rand . '.jpg');
                    $selectedImages [] = $rand; // We have an available image !
                    break;
                } elseif ($i > count($randImages)) {
                    // $selectedImages = array(); // Resetting the array for reuse
                    $center->setCenterImage('shinigami-laser_' . $rand . '.jpg');
                    break;
                }
            }
            $this->addReference('center'.$i, $center);
            $manager->persist($center);
        }

        $center = new Center();
        $center->setName('Shinigami Laser Online');
        $center->setPhone('0102030405');
        $center->setEmail('club-online@shinigami.com'); // set to lower case
        $center->setAddress("15 rue de Paris");
        $center->setZipCode("75001");
        $center->setCity("Paris");
        $center->setCountry('France');
        $center->setCenterCode(110);
        $center->setPublished(0);
        $this->addReference('center_1', $center);
        $manager->persist($center);

        $manager->flush();

    }

}
