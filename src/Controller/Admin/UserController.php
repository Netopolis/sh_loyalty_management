<?php

namespace App\Controller\Admin;

use App\Entity\Center;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\AdminUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/users")
 */
class UserController extends AbstractController
{
	private $routeRedirect = 'admin_login';
    /**
     * @Route("/", name="user_index", methods={"GET", "POST"})
     * @param UserRepository $userRepository
     * @param AdminUserService $adminService
     * @return Response
     */
    public function index(UserRepository $userRepository, AdminUserService $adminService): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
         */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        return $this->render('admin/user/index.html.twig', ['users' => $userRepository->findAllUsers(),
            'userData' => $userData
        ]);
    }

    /**
     * @Route("/by-center", name="user_index_by_center")
     * @param UserRepository $userRepository
     * @param AdminUserService $adminService
     * @return Response
     */
    public function indexByCenter(	UserRepository $userRepository, 
									AdminUserService $adminService): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
         */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        // first, get the admins
        $admins = $userRepository->findUnassignedAdmins();

        // then find the users (or staff members), by center
        $centers = $this->getDoctrine()
            ->getRepository(Center::class)
            ->findAll();


        return $this->render('admin/user/indexbycenter.html.twig', [
            'admins' => $admins,
            'centers' => $centers,
            'userData' => $userData
        ]);
    }

    /**
     * @Route("/inactive", name="user_inactive_index", methods={"GET", "POST"})
     * @param UserRepository $userRepository
     * @param AdminUserService $adminService
     * @return Response
     */
    public function indexInactive(	UserRepository $userRepository, 
									AdminUserService $adminService): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
         */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        return $this->render('admin/user/inactive_index.html.twig', ['users' => $userRepository->findAllInactiveUsers(),
            'userData' => $userData
        ]);
    }


    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @param AdminUserService $adminService
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function newUserAdmin(	Request $request, 
									AdminUserService $adminService,
                                     UserPasswordEncoderInterface $passwordEncoder): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
         */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword($passwordEncoder
                ->encodePassword($user, $user->getPassword()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'userData' => $userData
        ]);
    }

    /**
     * @Route("/{id}", name="user_show")
     * @param User $user
     * @param AdminUserService $adminService
     * @return Response
     */
    public function show(User $user, AdminUserService $adminService): Response
    {

		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        // Get the center where the user (staff member) works
        $center = $user->getCenter();

        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
            'center' => $center,
            'userData' => $userData
        ]);
    }

    /**
     * @Route("/edit/{id}", name="user_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param AdminUserService $adminService
     * @return Response
     */
    public function edit(	Request $request, 
							User $user,
							UserPasswordEncoderInterface $passwordEncoder,
							AdminUserService $adminService): Response
    {

		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Encode the password, if a new one was set
             */
            $user->setPassword($passwordEncoder
                ->encodePassword($user, $user->getPassword()));

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index', ['id' => $user->getId()]);
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'userData' => $userData
        ]);
    }

    /**
     * @Route("del/{id}", name="user_delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // In real life when an employee leaves, you must keep records
            // So we not delete anyone, but mark them as inactive
            $user->setIsActive(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/disabled/{id}", name="user_disable", methods={"GET"})
     * @param Request $request
     * @param User $user
     * @param AdminUserService $adminService
     * @return Response
     */
    public function disableUser(Request $request, User $user, AdminUserService $adminService): Response
    {

		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}
		// Mark the user as inactive
		$user->setIsActive(false);

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->flush();
		
		$this->addFlash('notice',
                'Membre ' . $user->getFirstName() . ' ' . $user->getLastName() . ' (#' . $user->getId() . ') a été désactivé');

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/enabled/{id}", name="user_enable", methods={"GET"})
     * @param Request $request
     * @param User $user
     * @param AdminUserService $adminService
     * @return Response
     */
    public function enableUser(	Request $request, 
								User $user,
								AdminUserService $adminService): Response
    {

		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}
		// Mark the user as active
		$user->setIsActive(true);

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->flush();
		
		$this->addFlash('notice',
                'Membre #' . $user->getId() . ' a été activé');

        return $this->redirectToRoute('user_index');
    }
}
