<?php
/**
 * Created by Hugues
 */

namespace App\Service;

use App\Entity\Customer;

/**
 * Sets the QRCode for new loyalty cards
 * along with useful information about the company, the customer and his card number
 */
class QRCodeEncoder
{

    /**
     * Generates QRCode for new loyalty cards
     * @param int $cardCode
     * @param string $custName
     * @return string
     */
    public function encodeQRCode(int $cardCode, string $custName) {

        $qRCode = 'Toutes les infos sur votre club:'.PHP_EOL
            . 'www.shinigami.com/shinigamilaserclubs'.PHP_EOL

            . 'Nos packs de jeu et offres:'.PHP_EOL
            . 'www.shinigami.com/le-jeu'.PHP_EOL

            . 'Votre compte, scores et carte de fidélité:'.PHP_EOL
            . 'www.shinigami.com/login'.PHP_EOL

            . 'A bientôt pour une partie !'.PHP_EOL
            . 'contact@shinigami.com'.PHP_EOL

            . 'Votre code carte ' . $cardCode . PHP_EOL
            . $custName ;

        return $qRCode;

    }

}