<?php

namespace App\Controller\Admin;

use App\Entity\Customer;
use App\Entity\LoyaltyCardRequest;
use App\Entity\CustomerActivity;
use App\Entity\User;
use App\Form\CustomerActivityType;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use App\Service\AdminUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/customers")
 */
class CustomerController extends AbstractController
{
	private $routeRedirect = 'admin_login';
    /**
 * @Route("/", name="customer_index", methods={"GET", "POST"})
 * @param CustomerRepository $customerRepository
 * @param AdminUserService $adminService
 * @return Response
 */
    public function index(CustomerRepository $customerRepository, AdminUserService $adminService): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
        */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        return $this->render('admin/customer/index.html.twig', [
			'customers' => $customerRepository->findAll(),
            'userData' => $userData
        ]);
    }

    /**
     * @Route("/activity", name="customer_activity_index", methods={"GET", "POST"})
     * @param CustomerRepository $customerRepository
     * @param AdminUserService $adminService
     * @return Response
     */
    public function indexActivity(	CustomerRepository $customerRepository, 
									AdminUserService $adminService): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
        */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        return $this->render('admin/customer/activity_index.html.twig', ['customers' => $customerRepository->findActiveCustomersActivities(),
            'userData' => $userData
        ]);
    }

    /**
     * @Route("/inactive", name="customer_inactive_index", methods={"GET", "POST"})
     * @param CustomerRepository $customerRepository
     * @param AdminUserService $adminService
     * @return Response
     */
    public function indexInactive(	CustomerRepository $customerRepository, 
									AdminUserService $adminService): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
        */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        return $this->render('admin/customer/inactive_index.html.twig', [
			'customers' => $customerRepository->findAllInactiveCustomers(),
            'userData' => $userData
        ]);
    }


    /**
     * @Route("/new", name="customer_new", methods={"GET","POST"})
     * @param Request $request
     * @param CustomerRepository $customerRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param AdminUserService $adminService
     * @return Response
     */
    public function newCustomer(	Request $request, 
									CustomerRepository $customerRepository,
									UserPasswordEncoderInterface $passwordEncoder,
									AdminUserService $adminService): Response
    {

        /** redirect to the login route, unless logged in and at least ROLE_STAFF
        */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        // Get the last customer code
        $lastCustomerCode = $customerRepository->getMaxCustomerCode();
        // Increment it by 1 to set the new code
        $customerCode = (int)$lastCustomerCode['lastCode'] +1;

        $customer = new Customer();
        $customer->setCustomerCode($customerCode);
        // call the form
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $customer->setPassword($passwordEncoder
                ->encodePassword($customer, $customer->getPassword()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $entityManager->flush();

            return $this->redirectToRoute('customer_index');
        }

        return $this->render('admin/customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
            'userData' => $userData
        ]);
    }

    /**
     * @Route("/{id}", name="customer_show", methods={"GET"})
     * @param Customer $customer
     * @param AdminUserService $adminService
     * @return Response
     */
    public function show(	Customer $customer, 
							AdminUserService $adminService): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
         */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        // Get preferred center + loyalty card, if any
        $center = $customer->getPreferredCenter();
        $loyaltyCard = $customer->getCards();

        $CardRequest = $this->getDoctrine()
            ->getRepository(LoyaltyCardRequest::class)
            ->findBy(["customer" => $customer]);

        // Get customer activity
        $customerActivity = $customer->getCustomerActivity();

        return $this->render('admin/customer/show.html.twig', [
            'customer' => $customer,
            'center' => $center,
            'loyalty_cards' => $loyaltyCard,
            'customer_activity' => $customerActivity,
            'userData' => $userData,
            'card_request' => $CardRequest
        ]);
    }

    /**
     * @Route("/edit/{id}", name="customer_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Customer $customer
     * @param AuthenticationUtils $authenticationUtils
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param AdminUserService $adminService
     * @return Response
     */
    public function edit(	Request $request, 
							Customer $customer,
							AuthenticationUtils $authenticationUtils,
							UserPasswordEncoderInterface $passwordEncoder,
							AdminUserService $adminService): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
         */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $customer->setPassword($passwordEncoder
                ->encodePassword($customer, $customer->getPassword()));

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('customer_index', ['id' => $customer->getId()]);
        }

        return $this->render('admin/customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
            'userData' => $userData
        ]);
    }

    /**
     * @Route("/activity/edit/{id}", name="customer_activity_edit", methods={"GET","POST"})
     * @param Request $request
     * @param CustomerActivity $customerActivity
     * @param AdminUserService $adminService
     * @return Response
     */
    public function editActivity(	Request $request, 
									CustomerActivity $customerActivity,  
									AdminUserService $adminService): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
         */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        // Get customer
        $customer = $customerActivity->getCustomer();

        $form = $this->createForm(CustomerActivityType::class, $customerActivity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", "Activités client mises à jour, merci !");

            return $this->redirectToRoute('customer_activity_edit', [
                'id' => $customerActivity->getId(),
            ]);
        }

        return $this->render('admin/customer/activity_edit.html.twig', [
            'customer' => $customer,
            'customer_activity' => $customerActivity,
            'form' => $form->createView(),
            'userData' => $userData
        ]);
    }


    /**
     * @Route("del/{id}", name="customer_delete", methods={"DELETE"})
     * @param Request $request
     * @param Customer $customer
     * @return Response
     */
    public function delete(Request $request, Customer $customer): Response
    {
		
        if ($this->isCsrfTokenValid('delete'.$customer->getId(), $request->request->get('_token'))) {
            // we should not delete anyone but mark them as inactive - now corrected
            $customer->setIsActive(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }

        return $this->redirectToRoute('customer_index');
    }

    /**
     * @Route("/disabled/{id}", name="customer_disable", methods={"GET"})
     * @param Request $request
     * @param AdminUserService $adminService
     * @param Customer $customer
     * @return Response
     */
    public function deleteFromList(	Request $request,
									AdminUserService $adminService, 
									Customer $customer): Response
    {
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}
		// we should not delete anyone but mark them as inactive
		$customer->setIsActive(false);

		$entityManager = $this->getDoctrine()->getManager();
		// $entityManager->remove($customer);
		$entityManager->flush();
		
		$this->addFlash('notice',
                'Client ' . $customer->getFirstName() . ' ' . $customer->getLastName() . ' (#' . $customer->getId() . ') a été désactivé');

        return $this->redirectToRoute('customer_index');
    }

    /**
     * @Route("/enabled/{id}", name="customer_enable", methods={"GET"})
     * @param Request $request
     * @param AdminUserService $adminService
     * @param Customer $customer
     * @return Response
     */
    public function enableCustomer(	Request $request,
									AdminUserService $adminService, 
									Customer $customer): Response
    {
		
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}
		
		// we should not delete anyone but mark them as inactive
		$customer->setIsActive(true);

		$entityManager = $this->getDoctrine()->getManager();
		// $entityManager->remove($customer);
		$entityManager->flush();
		
		$this->addFlash('notice',
                'Client #' . $customer->getId() . ' a été activé');

        return $this->redirectToRoute('customer_index');
    }
}
