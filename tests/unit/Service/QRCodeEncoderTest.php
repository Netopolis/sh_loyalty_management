<?php
/**
 * Created by Hugues
 */

namespace App\Tests\Unit\Service;

use App\Service\QRCodeEncoder;
use PHPUnit\Framework\TestCase;

/**
 *  Sets the QRCode for new loyalty cards
 */
class QRCodeEncoderTest extends TestCase
{

    /**
     * Generates QRCode for new loyalty cards
     * @param int $cardCode
     * @param string $custName
     * @return string
     */
    public function testEncodeQRCode() {

        $cardCode = 1111000083;
        $customerName = 'Samuel L. \'bad motherfucker\' Jackson';

        $QrcEn = new QRCodeEncoder();

        $qRCode = $QrcEn->encodeQRCode($cardCode, $customerName);

        $this->assertNotEmpty($qRCode);
        $this->assertContains((string)$cardCode, $qRCode);
        $this->assertContains('Samuel L. \'bad motherfucker\' Jackson', $qRCode);

    }

}