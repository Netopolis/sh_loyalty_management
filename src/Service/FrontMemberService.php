<?php
/**
 * Created by Hugues
 * 08/01/2019
 */

namespace App\Service;

use App\Entity\Customer;
use App\Entity\LoyaltyCardRequest;
use App\Repository\CenterRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Endroid\QrCode\Factory\QrCodeFactory;


/**
 */
class FrontMemberService
{

    /**
     * @param Customer $customer
     * @param QrCodeFactory $qrCodeFactory
     * @param ObjectManager $manager
     * @param CenterRepository $centerRepository
     * @return array
     */
    public function getInfoMember(Customer $customer, QrCodeFactory $qrCodeFactory, ObjectManager $manager, CenterRepository $centerRepository) {

        $loyaltyCards = $customer->getCards();

        if(count($loyaltyCards) > 0){
            $loyaltyCard = $loyaltyCards[0];
            $qrCodeText = $loyaltyCard->getQRCode();
            // Below, the options passed to the QrCodeFactory do not work, they are overridden in config -> packages -> qrcode.yaml
            $qrCode = $qrCodeFactory->create($qrCodeText, ['size' => 60, 'label' => false, 'logo_width' => 0, 'logo_height' => 0, 'error_correction_level' => 'medium']);
        }else{
            $qrCode = "";
        }

        $customerActivity = $customer->getCustomerActivity();

        $cardRequest = $manager->getRepository(LoyaltyCardRequest::class)
            ->findOneBy(['customer' => $customer->getId(), 'status' => 0]); /* */

        $generalInfo = [
            'centers' => $centerRepository->findPublishedCenters(),
            'customerActivity' => $customerActivity,
            'customerCardRequest' => $cardRequest,
            'loyalty_cards' => $loyaltyCards,
            'qr_code' => $qrCode
        ];

        return $generalInfo;

    }
	
}