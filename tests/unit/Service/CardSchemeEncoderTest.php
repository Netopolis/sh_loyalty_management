<?php
/**
 * Created by Hugues
 * 23/12/2018
 */

namespace App\Tests\Unit\Service;

use App\Entity\Center;
use App\Entity\Customer;
use App\Entity\LoyaltyCard;
use App\Service\CardSchemeEncoder;
use PHPUnit\Framework\TestCase;


class CardSchemeEncoderTest extends TestCase
{

    public $cardEn;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->cardEn = new CardSchemeEncoder();
    }

    /**
     * Test encoding of new loyalty cards
     */
    public function testEncode() {

        $centerCode = 111;
        $customerCode = 100071;
        $cardResultCode = 1111000713;


        $this->assertSame($cardResultCode, $this->cardEn->encode($centerCode, $customerCode));

    }

    /**
     * Test decoding of loyalty cards
     */
    public function testDecode() {

        $cardCode = 1131000715;
        $lc = new LoyaltyCard();
        $lc->setCardCode($cardCode);

        $centerCode = substr($cardCode, 0, 3);
        $customerCode = substr($cardCode, 3, 6);

        $arrDecode = $this->cardEn->decode($lc);

        $this->assertSame($centerCode, $arrDecode['centerCode']);
        $this->assertSame($customerCode, $arrDecode['customerCode']);

    }


}