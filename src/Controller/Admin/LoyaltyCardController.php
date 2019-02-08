<?php

namespace App\Controller\Admin;

use App\Entity\LoyaltyCard;
use App\Entity\Customer;
use App\Entity\User;
use App\Form\LoyaltyCardType;
use App\Repository\LoyaltyCardRepository;
use App\Service\AdminUserService;
use App\Service\CardSchemeEncoder;
use App\Service\QRCodeEncoder;
use Endroid\QrCode\Factory\QrCodeFactory;
use Endroid\QrCode\QrCode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

/**
 * @Route("/admin/loyalty/card")
 */
class LoyaltyCardController extends AbstractController
{
	private $routeRedirect = 'admin_login';
    /**
     * @Route("/", name="loyalty_card_index", methods={"GET", "POST"})
     * @param LoyaltyCardRepository $loyaltyCardRepository
     * @param AdminUserService $adminService
     * @return Response
     */
    public function index(	LoyaltyCardRepository $loyaltyCardRepository, 
							AdminUserService $adminService): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
         */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        //return $this->render('admin/loyalty_card/index.html.twig', ['loyalty_cards' => $loyaltyCardRepository->findAll()]);
        return $this->render('admin/loyalty_card/index.html.twig', [
            'loyalty_cards' => $loyaltyCardRepository->findAllCardsAndCustomers(),
            'userData' => $userData
        ]);
    }

    /**
     * @Route("/inactive", name="loyalty_card_inactive_index", methods={"GET", "POST"})
     * @param LoyaltyCardRepository $loyaltyCardRepository
     * @param AdminUserService $adminService
     * @return Response
     */
    public function indexInactive(	LoyaltyCardRepository $loyaltyCardRepository, 
									AdminUserService $adminService): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
         */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        return $this->render('admin/loyalty_card/inactive_index.html.twig', [
            'loyalty_cards' => $loyaltyCardRepository->findAllInactiveCardsAndCustomers(),
            'userData' => $userData
        ]);
    }


    /**
     * @Route("/new", name="loyalty_card_new", methods={"GET","POST"})
     * @param Request $request
     * @param AdminUserService $adminService
     * @param Registry $workflows
     * @return Response
     */
    public function newLoyaltyCard(	Request $request, 
									AdminUserService $adminService, 
									Registry $workflows): Response
    {

        /** redirect to the login route, unless logged in and at least ROLE_STAFF
         */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        $loyaltyCard = new LoyaltyCard();
        $form = $this->createForm(LoyaltyCardType::class, $loyaltyCard);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // create the new card's code with the CardSchemeEncoder service
            $centerCode = $loyaltyCard->getCenter()->getCenterCode();
            $customerCode = $loyaltyCard->getCustomer()->getCustomerCode();
            $cardEn = new CardSchemeEncoder();
            $cardCode = $cardEn->encode($centerCode, $customerCode);
            $loyaltyCard->setCardCode($cardCode);

            $QRCodeEncoder = new QRCodeEncoder();
            $customerName = $loyaltyCard->getCustomer()->getFullName();
            $qrCode = $QRCodeEncoder->encodeQRCode($loyaltyCard->getCardCode(), $customerName);
            $loyaltyCard->setQRCode($qrCode);

            // WorkFlow
            $workflow = $workflows->get($loyaltyCard);
            $workflow->apply($loyaltyCard, 'init_admin');

            // then persist it
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($loyaltyCard);
            $entityManager->flush();

            // return $this->redirectToRoute('loyalty_card_index');
			return $this->redirectToRoute('loyalty_card_show', ['id' => $loyaltyCard->getId()]);
        }

        return $this->render('admin/loyalty_card/new.html.twig', [
            'loyalty_card' => $loyaltyCard,
            'form' => $form->createView(),
            'userData' => $userData
        ]);
    }


    /**
     * @Route("/new/customer_{id<\d+>}", name="customer_new_card", methods={"GET","POST"})
     * @param Request $request
     * @param AdminUserService $adminService
     * @param Registry $workflows
     * @param Customer $customer
     * @return Response
     */
    public function newCustomerCard(	Request $request,
										AdminUserService $adminService, 
										Registry $workflows,
										Customer $customer): Response
    {

        /** redirect to the login route, unless logged in and at least ROLE_STAFF
         */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        $loyaltyCard = new LoyaltyCard();
		$loyaltyCard->setCustomer($customer);
        $form = $this->createForm(LoyaltyCardType::class, $loyaltyCard);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // create the new card's code with the CardSchemeEncoder service
            $centerCode = $loyaltyCard->getCenter()->getCenterCode();
            $customerCode = $loyaltyCard->getCustomer()->getCustomerCode();
            $cardEn = new CardSchemeEncoder();
            $cardCode = $cardEn->encode($centerCode, $customerCode);
            $loyaltyCard->setCardCode($cardCode);

            $QRCodeEncoder = new QRCodeEncoder();
            $customerName = $loyaltyCard->getCustomer()->getFullName();
            $qrCode = $QRCodeEncoder->encodeQRCode($loyaltyCard->getCardCode(), $customerName);
            $loyaltyCard->setQRCode($qrCode);

            // WorkFlow
            $workflow = $workflows->get($loyaltyCard);
            $workflow->apply($loyaltyCard, 'init_admin');

            // then persist it
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($loyaltyCard);
            $entityManager->flush();

			return $this->redirectToRoute('loyalty_card_edit', ['id' => $loyaltyCard->getId()]);
            //return $this->redirectToRoute('loyalty_card_index');
        }

        return $this->render('admin/loyalty_card/new.html.twig', [
            'loyalty_card' => $loyaltyCard,
            'form' => $form->createView(),
            'userData' => $userData
        ]);
    }


    /**
     * @Route("/{id}", name="loyalty_card_show", methods={"GET", "POST"})
     * @param LoyaltyCard $loyaltyCard
     * @param QrCodeFactory $qrCodeFactory
     * @param AdminUserService $adminService
     * @return Response
     */
    public function show(	LoyaltyCard $loyaltyCard, 
							QrCodeFactory $qrCodeFactory, 
							AdminUserService $adminService): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
         
        $this->denyAccessUnlessGranted('ROLE_STAFF');
        if ($this->isGranted('ROLE_STAFF')) {
            $userData = $adminService->getLegitimateUser($this->getUser());
        }*/
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        // Get the card's owner, and display his or her name with a link
        $customer = $loyaltyCard->getCustomer();
        // Same for the center
        $center =$loyaltyCard->getCenter();
        // Get $qrCodeText below, to display the values stored as text in the database
        $qrCodeText = $loyaltyCard->getQRCode();
        // Below, the options passed to the QrCodeFactory do not work - thanks, the bundle !
        // we override them in config -> packages -> endroid_qr_code.yaml
        $qrCode = $qrCodeFactory->create($qrCodeText, [
									'size' => 60, 
									'label' => false, 
									'logo_width' => 0, 
									'logo_height' => 0, 
									'error_correction_level' => 'medium']);

        return $this->render('admin/loyalty_card/show.html.twig', [
            'loyalty_card' => $loyaltyCard,
            'center' => $center,
            'customer' => $customer,
            'qr_code' => $qrCode,
            'userData' => $userData
        ]);
    }

    /**
     * @Route("/edit/{id}", name="loyalty_card_edit", methods={"GET","POST"})
     * @param Request $request
     * @param LoyaltyCard $loyaltyCard
     * @param AdminUserService $adminService
     * @return Response
     */
    public function edit(	Request $request, 
							LoyaltyCard $loyaltyCard, 
							AdminUserService $adminService): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
         */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        $form = $this->createForm(LoyaltyCardType::class, $loyaltyCard);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('loyalty_card_edit', ['id' => $loyaltyCard->getId()]);
        }

        return $this->render('admin/loyalty_card/edit.html.twig', [
            'loyalty_card' => $loyaltyCard,
            'form' => $form->createView(),
            'userData' => $userData
        ]);
    }

    /**
     * @Route("del/{id}", name="loyalty_card_delete", methods={"DELETE"})
     * @param Request $request
     * @param LoyaltyCard $loyaltyCard
     * @return Response
     */
    public function delete(	Request $request, 
							LoyaltyCard $loyaltyCard): Response
    {
        if ($this->isCsrfTokenValid('delete'.$loyaltyCard->getId(), $request->request->get('_token'))) {
            // We deactivate the card (we could add a timestamp), but we do not delete it

            $loyaltyCard->setIsValid(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }

        return $this->redirectToRoute('loyalty_card_index');
    }
	
		/**
     * @Route("/disabled/{id}", name="card_disable", methods={"GET"})
     * @param Request $request
     * @param LoyaltyCard $loyaltyCard
     * @return Response
     */
    public function disabledCard(Request $request, LoyaltyCard $loyaltyCard, AdminUserService $adminService): Response
    {

		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}
		// we do not delete, but mark the card as invalid
		$loyaltyCard->setIsValid(false);

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->flush();
		
		$this->addFlash('notice',
                'Carte #' . $loyaltyCard->getId() . ' a été désactivée');

        return $this->redirectToRoute('loyalty_card_index');
    }

    /**
     * @Route("/enabled/{id}", name="card_enable", methods={"GET"})
     * @param Request $request
     * @param LoyaltyCard $loyaltyCard
     * @param AdminUserService $adminService
     * @return Response
     */
    public function enableCard(	Request $request, 
								LoyaltyCard $loyaltyCard, 
								AdminUserService $adminService): Response
    {

		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}
		// Mark the card as valid
		$loyaltyCard->setIsValid(true);

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->flush();
		
		$this->addFlash('notice',
                'Carte #' . $loyaltyCard->getId() . ' a été activée');

        return $this->redirectToRoute('loyalty_card_index');
    }
}
