<?php

namespace App\Controller\Admin;

use App\Entity\Customer;
use App\Entity\LoyaltyCard;
use App\Entity\LoyaltyCardRequest;
use App\Entity\User;
use App\Repository\CenterRepository;
use App\Repository\CustomerRepository;
use App\Service\AdminUserService;
use App\Service\CardSchemeEncoder;
use App\Service\QRCodeEncoder;
use App\Service\YamlStatsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Exception\LogicException;

// use Symfony\Component\HttpFoundation\Response;

class AdminController extends AbstractController
{

	private $routeRedirect = 'admin_login';
    /**
     * @Route("/admin", name="admin_home_dispatch", methods={"GET","POST"})
     * @param AdminUserService $adminService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAdmin(AdminUserService $adminService)
    {

		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}else{
			return $this->redirectToRoute('admin_index');
		}
		
        
    }
	
    /**
     * @Route("/admin/index_old", name="admin_index_old", methods={"GET","POST"})
     * @param AdminUserService $adminService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(AdminUserService $adminService)
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
         * If Ok, get user details
         */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        return $this->render('admin/index_admin.html.twig', [
            'userData' => $userData
        ]);
    }

    /**
     * Search bar display in left menu
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchBar()
    {
        $form = $this->createFormBuilder(null) // might as well pass null, as this is not an EntityType
            ->add("search", SearchType::class, [
                'attr' => [
                    'placeholder' => 'Recherche',
                    'title' => 'Client ou carte, premières lettres ou chiffres'
                ]
            ])
            ->setAction($this->generateUrl('admin_search'))
            ->setMethod('POST')
            ->getForm();

        return $this->render('admin/components/_searchbar.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Search results for customers and / or loyalty cards, depends on searchBar above
     * @Route("/admin/search", name="admin_search")
     * @param CustomerRepository $repository
     * @param Request $request
     * @param AdminUserService $adminService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function AdminSearchresults(	CustomerRepository $repository, 
										Request $request, 
										AdminUserService $adminService)
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

        // 'form' is sent in the request through POST with key 'search' for the data
        // Sent to the correct route, though the formBuilder ->setAction->generateUrl
        $data = $request->request->get('form');
        $results = $repository->findBySearchCriteria($data['search']);

        return $this->render('admin/customer/searchresults.html.twig', [
            'results' => $results,
            'userData' =>$userData
        ]);

    }

    /**
     * @Route("/admin/dashboard/", name="admin_index", methods={"GET","POST"})
     * @param CenterRepository $repository
     * @param AdminUserService $adminService
     * @param YamlStatsProvider $yamlStatsProvider
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashBoard(	CenterRepository $repository, 
								AdminUserService $adminService, 
								YamlStatsProvider $yamlStatsProvider) {

        /** redirect to the login route, unless logged in and at least ROLE_STAFF
         * If ok, get user details
        */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        // do some COUNTs against the database to gather stats
        $centersTotal = $repository->countCenters();
        $customersTotal = $this->getDoctrine()
            ->getRepository(Customer::class)
            ->countActiveCustomers();
        $loyaltyCardsTotal = $this->getDoctrine()
            ->getRepository(LoyaltyCard::class)
            ->countValidLoyaltyCards();
        $loyaltyPointsEarnedTotal = $this->getDoctrine()
            ->getRepository(LoyaltyCard::class)
            ->loyaltyPointsEarnedTotal();
        $staffTotal = $this->getDoctrine()
            ->getRepository(User::class)
            ->countActiveStaff();

        // get other stats from the yaml file
        $stats = $yamlStatsProvider->getStats();

        $center_id = $this->getUser()->getCenter()->getId();


        // checking permissions for the card request workflow
        if ($this->isGranted('ROLE_ADMIN')) {
            $CardRequest = $this->getDoctrine()
                ->getRepository(LoyaltyCardRequest::class)
                ->findBy(["status" => 0]);
        }else{
            $CardRequest = $this->getDoctrine()
                ->getRepository(LoyaltyCardRequest::class)
                ->findNewRequest($center_id);
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $CardDisposal = $this->getDoctrine()
                ->getRepository(LoyaltyCard::class)
                ->findBy(["status" => "validated"]);
        }else{
            $CardDisposal = $this->getDoctrine()
                ->getRepository(LoyaltyCard::class)
                ->findValidatedCards();
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $CardToWithdraw = $this->getDoctrine()
                ->getRepository(LoyaltyCard::class)
                ->findBy(["status" => "supplied"]);
        }else{
            $CardToWithdraw = $this->getDoctrine()
                ->getRepository(LoyaltyCard::class)
                ->findDeliveredCards();
        }



        return $this->render('admin/center/board.html.twig', [
            'centersTotal' => $centersTotal,
            'customersTotal' => $customersTotal,
            'loyaltyCardsTotal' => $loyaltyCardsTotal,
            'loyaltyPointsEarnedTotal' => $loyaltyPointsEarnedTotal,
            'staffTotal' => $staffTotal,
            'stats' => $stats,
            'card_request' => $CardRequest,
            'card_disposal' => $CardDisposal,
            'card_supplied' => $CardToWithdraw,
            'userData' => $userData
        ]);
    }

    /**
     * @Route("/admin/loyalty/manager", name="admin_loyalty_manager", methods={"GET","POST"})
     * @param AdminUserService $adminService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loyaltyManager(AdminUserService $adminService) {
        // several things to design and implement here

        /** redirect to the login route, unless logged in and at least ROLE_STAFF
        */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        return $this->render('admin/center/loyaltyrewards.html.twig', [
            'userData' => $userData
        ]);
    }

    /**
     * @Route("/admin/cardvalidate/{id}", name="admin_card_validate")
     * @param LoyaltyCardRequest $loyaltyCardRequest
     * @param AdminUserService $adminService
     * @param Registry $workflows
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function ValidateCard(LoyaltyCardRequest $loyaltyCardRequest, 
								AdminUserService $adminService,
								Registry $workflows) {
		
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        $loyaltyCardRequest->setStatus(1);
        $this->getDoctrine()->getManager()->flush();

        $customer = $loyaltyCardRequest->getCustomer();
        $center = $customer->getPreferredCenter();

        $loyaltyCard = new LoyaltyCard();
        $loyaltyCard->setCustomer($customer);
        $loyaltyCard->setCenter($center);

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
        $workflow->apply($loyaltyCard, 'init');

        # Insertion en BDD
        // then persist it
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($loyaltyCard);
        $entityManager->flush();

        # Notification
        $this->addFlash('notice',
            'Nouvelle carte validée pour client #'.$customer->getId().' '. $customer->getFirstName() . ' '. $customer->getLastName() . '');

        return $this->redirectToRoute("admin_index");

    }

    /**
     * @Route("/admin/cardrefuse/{id}", name="admin_card_refuse")
     * @param LoyaltyCardRequest $loyaltyCardRequest
     * @param AdminUserService $adminService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function RefuseCard(LoyaltyCardRequest $loyaltyCardRequest, AdminUserService $adminService) {

		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}
		
        $loyaltyCardRequest->setStatus(2);
        $this->getDoctrine()->getManager()->flush();

        $customer = $loyaltyCardRequest->getCustomer();

        # Notification
        $this->addFlash('notice',
            'Enregistrement du refus de carte pour #'.$customer->getId().' '. $customer->getFirstName() . ' '. $customer->getLastName() . '');

        return $this->redirectToRoute("admin_index");

    }

    //

    /**
     * @Route("/admin/cardinformcustomer/{id}", name="admin_inform_customer_card")
     * @param LoyaltyCard $loyaltyCard
     * @param AdminUserService $adminService
     * @param Registry $workflows
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function InformCustomerCard(LoyaltyCard $loyaltyCard, 
										AdminUserService $adminService,
										Registry $workflows) {

		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}		
		
        $customer = $loyaltyCard->getCustomer();

        // Mail au client :
        // use swiftMailer
        //

        // WorkFlow
        $workflow = $workflows->get($loyaltyCard);
        $workflow->apply($loyaltyCard, 'to_take');

        # Insertion en BDD
        // then persist it
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($loyaltyCard);
        $entityManager->flush();

        # Notification
        $this->addFlash('notice',
            'Email de notification de nouvelle carte pour #'.$customer->getId().' '. $customer->getFirstName() . ' '. $customer->getLastName() . '');

        return $this->redirectToRoute("admin_index");

    }

    /**
     * @Route("/admin/cardwithdraw/{id}", name="admin_card_withdraw")
     * @param LoyaltyCard $loyaltyCard
     * @param AdminUserService $adminService
     * @param Registry $workflows
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function CustomerCardWithDraw(	LoyaltyCard $loyaltyCard, 
											AdminUserService $adminService,
											Registry $workflows) {

		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}
		
        $customer = $loyaltyCard->getCustomer();

        // WorkFlow
        $workflow = $workflows->get($loyaltyCard);
        $workflow->apply($loyaltyCard, 'to_delivered_to_customer');

        # Insertion en BDD
        // then persist it
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($loyaltyCard);
        $entityManager->flush();

        # Notification
        $this->addFlash('notice',
            'Mise à jour de la carte du client #'.$customer->getId().' '. $customer->getFirstName() . ' '. $customer->getLastName() . '');

        return $this->redirectToRoute("admin_index");

    }
}
