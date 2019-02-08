<?php
/**
 * Created by Hugues
 */

namespace App\Controller\Front;

use App\Entity\Customer;
use App\Entity\CustomerActivity;
use App\Entity\LoyaltyCardRequest;
use App\Form\CustomerType;
use App\Form\FrontCustomerType;
use App\Repository\CenterRepository;
use App\Repository\CustomerRepository;
use App\Service\FrontMemberService;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Endroid\QrCode\Factory\QrCodeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/")
 */
class AccountController extends AbstractController
{

	public function getMember(){
		
		$MemberId = $this->getUser()->getId();
		
		
		$isAdminOrStaff = (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || 
							in_array('ROLE_STAFF', $this->getUser()->getRoles())) ? true : false;
							
		if($isAdminOrStaff){
		   return false;      
		}else{
			$member = $this->getDoctrine()
             ->getRepository(Customer::class)
             ->find($MemberId);
			 
			 return $member;
		}
	}

    /**
     * Default route to customer account
     * @Route("/account/", name="account", methods={"GET", "POST"})
     * @param Request $request
     * @param CenterRepository $centerRepository
     * @param QrCodeFactory $qrCodeFactory
     * @param ObjectManager $manager
     * @param FrontMemberService $FrontMemberService
     * @return Response
     */
    public function accountIndex(Request $request, CenterRepository $centerRepository, QrCodeFactory $qrCodeFactory, ObjectManager $manager, FrontMemberService $FrontMemberService): Response {        

        // If the customer is not logged, redirect him to the login page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		$customer = $this->getMember();
		
		if(!$customer){
			return $this->redirectToRoute("home");
		}

		// call the service to get all that we need
        $generalInfo = $FrontMemberService->getInfoMember($customer, $qrCodeFactory, $manager, $centerRepository);

        return $this->render('front/account/account_index.html.twig', [
            'centers' => $generalInfo['centers'],
            'customer' => $customer,
            'customer_image' => $customer->getImageProfile(),
            'customer_activity' => $generalInfo['customerActivity'],
            'loyalty_cards' => $generalInfo['loyalty_cards'],
            'card_request' => $generalInfo['customerCardRequest'],
            'qr_code' => $generalInfo['qr_code'],
            'member_menu' => "scores"
        ]);

    }

    /**
     * Scores route to customer account
     * @Route("/account/member/{part}", name="account_member")
     * @param $part
     * @param Request $request
     * @param CenterRepository $centerRepository
     * @param QrCodeFactory $qrCodeFactory
     * @param ObjectManager $manager
     * @param FrontMemberService $FrontMemberService
     * @return Response
     */
    public function accountScores($part, Request $request, CenterRepository $centerRepository, QrCodeFactory $qrCodeFactory, ObjectManager $manager, FrontMemberService $FrontMemberService): Response {

        // $customerId = $this->getUser()->getId();

        // If the customer is not logged, redirect him to the login page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		$customer = $this->getMember();
		
		if(!$customer){
			return $this->redirectToRoute("home");
		}

        $generalInfo = $FrontMemberService->getInfoMember($customer, $qrCodeFactory, $manager, $centerRepository);

        return $this->render('front/account/account_index.html.twig', [
            'centers' => $generalInfo['centers'],
            'customer' => $customer,
            'customer_image' => $customer->getImageProfile(),
            'customer_activity' => $generalInfo['customerActivity'],
            'loyalty_cards' => $generalInfo['loyalty_cards'],
            'card_request' => $generalInfo['customerCardRequest'],
            'qr_code' => $generalInfo['qr_code'],
            'member_menu' => $part
        ]);

    }

    /**
     * Requests for loyalty cards, sent by customers
     * @Route("/account/cardrequest/", name="card_request", methods={"GET", "POST"})
     * @param Request $request
     * @param Customer $customer
     * @param CenterRepository $centerRepository
     * @return Response
     */
    public function cardRequest(Request $request, CenterRepository $centerRepository): Response {

        // If the customer is not logged, redirect him to the login page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

			
		$customer = $this->getMember();
		
		if(!$customer){
			return $this->redirectToRoute("home");
		}

        // compare, check if the customer in session is the right person, and if he doesn't already have a card
        if (count($customer->getCards()) > 0) {
            return $this->redirectToRoute('home');
        }

        $loyaltyCardRequest = New LoyaltyCardRequest();
        $loyaltyCardRequest->setCustomer($customer);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($loyaltyCardRequest);
        $entityManager->flush();

        // redirecting to the account page
        return $this->redirectToRoute("account_member", ["part" => "avantages"]);
    }

    /** Need customer.id, to know if the card request was sent,
     * Then need to know the status of the loyalty card (to display the correct information)
     * We will switch the views, or rather content in the wiews depending on the situation
     */
    public function cardDisplay() {
        return $this->render('front/components/_card_display.html.twig'/*, [
                'dataNeeded' => $dataNeeded
            ]*/);
    }

    /**
     * @Route("/account/infos-edit/", name="member_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param CenterRepository $centerRepository
     * @param Packages $packages
     * @param QrCodeFactory $qrCodeFactory
     * @param FrontMemberService $FrontMemberService
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function memberEdit(Request $request, CenterRepository $centerRepository, Packages $packages, QrCodeFactory $qrCodeFactory, FrontMemberService $FrontMemberService, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder){
		
		$img_profile_name = "profil_default_image.png";

        // If the customer is not logged, redirect him to the login page
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

		$customer = $this->getMember();
		
		if(!$customer){
			return $this->redirectToRoute("home");
		}


        if(!empty($customer->getImageProfile())){
			
			$img_profile_name = $customer->getImageProfile();
			
            # On passe à notre formulaire l'URL de l'image
            $options = [
                'image_url' => $packages->getUrl('images/members/profile_images/'
                    . $customer->getImageProfile())
            ];

            # Récupération de l'image
            $featuredImageName = $customer->getImageProfile();

            # Notre formulaire attend une instance de File pour l'edition
            # de l'image
            $customer->setImageProfile(
                new File($this->getParameter('members_profile_images_dir')
                    . '/' . $featuredImageName)
            );
        }

        $form = $this->createForm(FrontCustomerType::class, $customer, $options);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $featuredImage */
            $featuredImage = $customer->getImageProfile();


            if($featuredImage != null){
                $fileName =  uniqid("imgp_") . "_". $customer->getId()
                    . '.' . $featuredImage->guessExtension();

                try {
                    $featuredImage->move(
                        $this->getParameter('members_profile_images_dir'),
                        $fileName
                    );
                } catch (FileException $e) {

                }
            }else{
                $fileName =  $img_profile_name;
            }

            # Mise à jour de l'image
            $customer->setImageProfile($fileName);

            $customer->setPassword($passwordEncoder
                ->encodePassword($customer, $customer->getPassword()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $entityManager->flush();

            $this->addFlash('notice',
                'Félicitation, votre profil a été mis à jour !');				
				
			return $this->redirectToRoute("member_edit");				

        }

        $generalInfo = $FrontMemberService->getInfoMember($customer, $qrCodeFactory, $manager, $centerRepository);

        return $this->render('front/account/account_edit.html.twig', [
            'centers' => $generalInfo['centers'],
            'customer' => $customer,
			'customer_image' => $img_profile_name,
            'customer_activity' => $generalInfo['customerActivity'],
            'loyalty_cards' => $generalInfo['loyalty_cards'],
            'card_request' => $generalInfo['customerCardRequest'],
            'qr_code' => $generalInfo['qr_code'],
            'member_menu' => "profile",
            'form' => $form->createView()
        ]);
    }
    
}