<?php

namespace App\Controller\Admin;

use App\Entity\Center;
use App\Entity\Customer;
use App\Entity\User;
use App\Form\CenterType;
use App\Repository\CenterRepository;
use App\Service\AdminUserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/centers")
 */
class CenterController extends AbstractController
{
	
	private $routeRedirect = 'admin_login';
    /**
     * @Route("/", name="center_index", methods={"GET", "POST"})
     * @param CenterRepository $centerRepository
     * @param AdminUserService $adminService
     * @return Response
     */
    public function index(CenterRepository $centerRepository, AdminUserService $adminService): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
        */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        return $this->render('admin/center/index.html.twig', ['centers' => $centerRepository->findAll(),
            'userData' => $userData
        ]);
    }

    /**
     * @Route("/new", name="center_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param AdminUserService $adminService
     * @return Response
     */
    public function newCenter(Request $request, AdminUserService $adminService): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
        */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        // Get the last center code
        $lastCenterCode = $this->getDoctrine()
            ->getRepository(Center::class)
            ->getMaxCenterCode();
        // Increment it by 1 to determine the new center code
        $centerCode = (int)$lastCenterCode['lastCode'] +1;

        $center = new Center();
        $center->setCenterCode($centerCode);
		
        // call the form
        $form = $this->createForm(CenterType::class, $center);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $featuredImage */
            $featuredImage = $center->getCenterImage();

			if($featuredImage != null){
				$fileName = $center->getSlug() . "_" . $center->getId()
					. '.' . $featuredImage->guessExtension();
	
				try {
					$featuredImage->move(
						$this->getParameter('centers_images_dir'),
						$fileName
					);
				} catch (FileException $e) {
	
				}
	
				# Mise à jour de l'image
				$center->setCenterImage($fileName);
			}

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($center);
            $entityManager->flush();

            return $this->redirectToRoute('center_edit', ['id' => $center->getId()]);
        }

        return $this->render('admin/center/new.html.twig', [
            'center' => $center,
            'form' => $form->createView(),
            'userData' => $userData
        ]);
    }


    /**
     * @Route("/{id}", name="center_show", methods={"GET", "POST"})
     * @param AdminUserService $adminService
     * @param Center|null $center
     * @return Response
     */
    public function show(AdminUserService $adminService, Center $center = null): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
        */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        if ($center === null) {
            return $this->redirectToRoute('admin_index', [],
                Response::HTTP_TEMPORARY_REDIRECT);
        }

        // Get the customers of this center, and display them below
        $customers = $this->getDoctrine()
            ->getRepository(Customer::class)
            ->findCustomersByCenter($center->getId());

        return $this->render('admin/center/show.html.twig', [
            'center' => $center,
            'customers' => $customers,
            'userData' => $userData
        ]);
    }


    /**
     * @Route("/edit/{id}", name="center_edit", methods={"GET","POST"})
     * @param Center $center
     * @param Request $request
     * @param Packages $packages
     * @param AdminUserService $adminService
     * @return Response
     */
    public function edit(	Request $request, 
							Center $center, 
							AdminUserService $adminService, 
							Packages $packages): Response
    {
        /** redirect to the login route, unless logged in and at least ROLE_STAFF
        */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}

        $options = ['image_url' => "", 'image_name' => ""];

        if(!empty($center->getCenterImage())){
            # On passe à notre formulaire l'URL de la featuredImage
            $options = [
                'image_url' => $packages->getUrl('images/centers/'
                    . $center->getCenterImage()),
				'image_name' => $center->getCenterImage()
            ];

            # Récupération de l'image
            $featuredImageName = $center->getCenterImage();

            # Notre formulaire attend une instance de File pour l'edition
            # de la featuredImage
            $center->setCenterImage(
                new File($this->getParameter('centers_images_dir')
                    . '/' . $featuredImageName)
            );			
			
        }

        $form = $this->createForm(CenterType::class, $center, $options);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $featuredImage */
            $featuredImage = $center->getCenterImage();

			
			if($featuredImage == null){
				// echo $old_img = $form->getConfig()->getOptions()['attr'];
				$fileName = $form->getConfig()->getOptions()['image_name'];
				
			}else{
	
				$fileName = $center->getSlug() . "_" . $center->getId()
					. '.' . $featuredImage->guessExtension();
	
				try {
					$featuredImage->move(
						$this->getParameter('centers_images_dir'),
						$fileName
					);
				} catch (FileException $e) {
	
				}
	
				# Mise à jour de l'image
				// $center->setCenterImage($fileName);				
				
			}
			
			$center->setCenterImage($fileName);

			$this->getDoctrine()->getManager()->flush();
            
			
			# Notification
            $this->addFlash('notice',
                'Félicitation, Centre mis à jour avec succès !');

            return $this->redirectToRoute('center_edit', ['id' => $center->getId()]);
        }

        return $this->render('admin/center/edit.html.twig', [
            'center' => $center,
            'form' => $form->createView(),
            'userData' => $userData
        ]);
    }

    /**
     * @Route("del/{id}", name="center_delete", methods={"DELETE"})
     * @param Request $request
     * @param AdminUserService $adminService
     * @param Center $center
     * @return Response
     */
    public function delete(	Request $request, 
							AdminUserService $adminService,
							Center $center): Response
    {
        /** Only allowed for administrators
         */
		$userData = $adminService->getLegitimateUser($this->getUser());
        if(!$userData){
			return $this->redirectToRoute($this->routeRedirect);
		}


        if ($this->isCsrfTokenValid('delete'.$center->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($center);
            $entityManager->flush();
        }

        return $this->redirectToRoute('center_index');
    }
}