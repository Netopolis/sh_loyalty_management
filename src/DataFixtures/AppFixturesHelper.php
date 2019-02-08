<?php
/**
 * Created by Hugues
 * 23/12/2018
 */

namespace App\DataFixtures;


/**
 * Used to generate names, addresses, cities and zipcodes for several fixtures / entities
 * There was no need to make a service out of it - it's simply used to populate the database
 */
class AppFixturesHelper
{

    /**
     * Returns a random firstname - for staff and customers
     * @return string
     */
    public function generateFirstName() {
        $firstNames = array(
            'Jonathan', 'Anthony', 'Robert', 'Louise', 'Laure', 'Camelia', 'Augustine', 'Christine', 'Luz', 'Diego', 'Linda', 'Thomas', 'Georgine', 'Leia', 'Alejandro', 'Matthieu', 'Alice', 'Stephanie', 'Edmond', 'Chouquette', 'Buffy', 'Pascal', 'Juliette', 'Balthazar', 'Luc', 'Emma', 'Helmut', 'Margarete', 'Susie', 'Jean', 'Tomi', 'Corwyn', 'Blake', 'Hélène', 'Chloé', 'Carla', 'Bernard', 'Jeanne', 'Orianne', 'Laurent', 'Benoit', 'Sebastien', 'Thibault', 'Christophe', 'Rebecca', 'Adeline', 'Michel', 'Michèle', 'Ruby', 'Fatima', 'Rachel', 'Marc', 'Cathie', 'Samantha', 'Lilibeth', 'Hugo', 'Renaud', 'Patou', 'Patrice', 'Riton', 'Marc', 'Taoufik', 'Abdeslam', 'Barbara', 'Mick', 'Mario', 'Paula', 'Frédéric');

        $firstName = $firstNames[mt_rand( 0 , count($firstNames) -1)];
        return $firstName;
    }


    /**
     * Returns a random lastname - with lots of common names in burgundy
     * @return string
     */
    public function generateLastName() {
        $lastNames = array(
            'Rousseau', 'Senna', 'Moreau', 'Leblanc', 'Legrand', 'Dubois', 'Schilgen', 'Roy', 'Wrona', 'Durand', 'Lang', 'Fetzer', 'Schroeder', 'Block', 'Truffert', 'Fleishman', 'Garcia', 'Joly', 'Lupo', 'Singer', 'Roux', 'Perrot', 'Chapuis', 'Simon', 'Bonnot', 'Berthier', 'Michaud', 'Moriarty', 'Lambert', 'Wiers', 'Girard', 'Thevenet', 'Weber', 'Renaud', 'De Soto', 'Pelletier', 'Kazmierczak', 'Marceau', 'Guillemette', 'Guyot', 'Castagner', 'Kucera', 'Mercier', 'Garnier', 'Gautier', 'Perrin', 'Byron', 'Nguyen', 'Holmes', 'Mercier', 'Pecora', 'Liegeard', 'Gonzales', 'Berthelot', 'Saadoun', 'Petitjean', 'Guérin', 'Gautheron', 'Bertrand', 'Royer', 'Kadri', 'Daoudi', 'Rousselet', 'De Mainmorte', 'Boulanger', 'Maillard', 'Buisson', 'Toussaint', 'Vial', 'Tellier', 'Simonnet');

        $lastName = $lastNames[mt_rand( 0 , count($lastNames) -1)];
        return $lastName;
    }


    /**
     * Returns a random nickname - used for customers only
     * @return string
     */
    public function generateNickName() {
        $nickNames = array(
            'Judge Dredd', 'Cowboy', 'Nacho', 'Hatchet', 'Killer', 'Cobra', 'Shooter', 'The Dude', 'Neuromancer', 'Bingo', 'Crash', 'Thunder', 'Freak', 'Bliss', 'Bang Bang', 'Mute', 'Dancer', 'Alfa', 'Bring em', 'Starsky', 'KissCool', 'White Rabbit', 'Neo', 'Darth Maul', 'Skywalker', 'Shepard', '007', 'Pirotess', 'Ghost', 'Bazooka', 'Solo', 'Kiss Kiss', 'Tombola', 'Tombstone', 'Chéri chéri', 'The One', 'The Kid', 'Gotcha', 'Got a gun', 'Can\'t wait', 'Tough Luck', 'You\'re toast', 'Double portion', 'I\'ll be back', 'Tango', 'Boum Boum', 'Robocop', 'Laser eyes', 'Sinner', 'Let die', 'Ciao bello', 'Gladiator', 'Flash', 'R3d AleRt', 'Sniper', 'Eliminator', 'Flagship', 'Sights on you', 'Eyes on target', 'Specter', 'Stalker', 'Ice', 'Predator', 'Shadow', 'Blitz', 'Bandit', 'Shadow', 'Jackpot', 'Artillerie', 'Duke', 'Valkyrie');

        $nickName = $nickNames[mt_rand(0, count($nickNames) - 1)];
        return $nickName;
    }


    /**
     * Returns a random date, between an interval
     * Used for customers birthday date, between -40 years and -20 years - tweak this if necessary, but laser players are young
     * Also used for loyalty cards' date of issue, between -20 months and -2 months ago
     * @param string $startDate
     * @param string $endDate
     * @return \DateTime
     * @throws \Exception
     */
    public function generateRandDate($startDate, $endDate) {
            $startTimestamp = $startDate instanceof \DateTime ? $startDate->getTimestamp() : strtotime($startDate);
            $endTimestamp = $endDate instanceof \DateTime ? $endDate->getTimestamp() : strtotime($endDate);
            if ($startTimestamp > $endTimestamp) {
                throw new \InvalidArgumentException('startDate must be inferior to endDate.');
            }
            $val= mt_rand($startTimestamp, $endTimestamp);

            // converting random int to string with function date()
            $stringDate = date("j-n-Y",$val);
            // converting string to DateTime object
            $date = new \DateTime($stringDate);
            return $date;
    }


    /**
     * Returns a concatenated address - based on existing streets in burgundy
     * @return string
     */
    public function generateAddress() {
        $streetPrefix = array('rue', 'rue', 'square', 'chemin', 'avenue', 'boulevard', 'place', 'impasse', 'allée', 'promenade', 'voie');
        $streetSuffix = array('des acacias', 'des rosiers', 'des poètes', 'Jean Jaurès', 'Jules Ferry', 'Pasteur', 'Debussy', 'Victor Hugo', 'bonnefoi', 'des pucelles', 'des muguets', 'des violettes', 'de la cerisaie', 'des petits saules', 'des merisiers', 'des sorbiers', 'du bon puits', 'de la Vieille-Bouderie', 'des deux écus', 'du château', 'de la harpe', 'de la croix blanche', 'saint-martin', 'du cherche midi', 'des myosotis', 'saint-eustache', 'sainte-geneviève', 'du mouton', 'du sabot', 'du perche', 'garlande', 'du millepertuis', 'Louis Aragon', 'Jean Anouilh', 'Beethoven', 'Champollion', 'Charcot', 'Frédéric Chopin', 'Fyot De La Marche', 'Carnot', 'Gregoire De Tours', 'Jean Mermoz', 'Philippe Le Bon', 'Philippe Le Hardi', 'Saint-Exupery', 'des ardennes', 'des arandes', 'des argentières', 'des valendons', 'de l\'alsace', 'du beaujolais', 'de bel air', 'de bellevue', 'de belfort', 'beranger', 'dunant', 'de la cascade',  'des carrières blanches', 'du castel', 'des cents écus', 'du chapitre', 'des petites roches', 'du pré versé', 'du clos bizot', 'du clos vougeot', 'des creuzots', 'du vieux collège', 'des Ducs de Bourgogne', 'des grands champs', 'des grésilles', 'longepierre', 'de la libération', 'de la petite monnaie', 'des moulins', 'du petit potet', 'de la sablière', 'des saunières', "de la tonnelle");

        $address = mt_rand(2, 60). ' ' . $streetPrefix[mt_rand( 0 , count($streetPrefix) -1)] . ' ' . $streetSuffix[mt_rand( 0 , count($streetSuffix) -1)];
        return $address;
    }


    /**
     * returns a array with a city and its zipCode - based on existing major cities of burgundy
     * @return array
     */
    public function generateCity() {
        $burgundyCities = array (array('city'=>'Dijon', 'zipCode'=>'21000'),
            array('city'=>'Dijon', 'zipCode'=>'21000'),
            array('city'=>'Chalon-sur-Saône', 'zipCode'=>'71530'),
            array('city'=>'Dijon', 'zipCode'=>'21000'),
            array('city'=>'Nevers', 'zipCode'=>'58000'),
            array('city'=>'Auxerre', 'zipCode'=>'89000'),
            array('city'=>'Mâcon', 'zipCode'=>'71870'),
            array('city'=>'Sens', 'zipCode'=>'89100'),
            array('city'=>'Le Creusot', 'zipCode'=>'71200'),
            array('city'=>'Beaune', 'zipCode'=>'21200'),
            array('city'=>'Montceau-les-Mines', 'zipCode'=>'71300'),
            array('city'=>'Autun', 'zipCode'=>'71400'),
            array('city'=>'Joigny', 'zipCode'=>'89300'),
            array('city'=>'Quetigny', 'zipCode'=>'21800'),
            array('city'=>'Longvic', 'zipCode'=>'21600'),
            array('city'=>'Quetigny', 'zipCode'=>'21800'),
            array('city'=>'Paray-le-Monial', 'zipCode'=>'71600'),
            array('city'=>'Saint-Vallier', 'zipCode'=>'71230'),
            array('city'=>'Varennes-Vauzelles', 'zipCode'=>'58640'),
            array('city'=>'Talant', 'zipCode'=>'21240'),
            array('city'=>'Saint-Georges-sur-Baulche', 'zipCode'=>'89000'),
            array('city'=>'Saint-Clément', 'zipCode'=>'89100'));

        // draw a random city
        $result = $burgundyCities[mt_rand( 0 , count($burgundyCities) -1)];
        return $result;

    }


    /**
     * Takes a utf_8 string and returns it without accents
     * @param $str
     * @return string
     */
    public function stripAccents($str) {
        return strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }


    /**
     * Get the difference between two dates in weeks
     * @param $date1
     * @param $date2
     * @return float
     */
    public function dateDiffInWeeks($date1, $date2)
    {
        if ($date1 < $date2) {
            $first = \DateTime::createFromFormat('m/d/Y', $date1);
            $second = \DateTime::createFromFormat('m/d/Y', $date2);
            return floor($first->diff($second)->days / 7);
        } else {
            $first = \DateTime::createFromFormat('m/d/Y', $date1);
            $second = \DateTime::createFromFormat('m/d/Y', $date2);
            return floor($second->diff($first)->days / 7);
        }
    }


    /**
     * Returns a random float, used for setting customer activity
     * @param $min
     * @param $max
     * @return float|int
     */
    public function randomFloat($min, $max) {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

}