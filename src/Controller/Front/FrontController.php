<?php

namespace App\Controller\Front;

use App\Entity\Center;
use App\Entity\Customer;
use App\Entity\CustomerActivity;
use App\Repository\CenterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{

    /**
     * @Route("/", name="home")
     * @param CenterRepository $centerRepository
     * @return Response
     */
    public function index(CenterRepository $centerRepository)
    {

        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
            'centers' => $centerRepository->findPublishedCenters()
        ]);
    }


    /**
     * @Route("/{slug}/{id<\d+>}", name="centers_infos")
     * @param $id
     * @param $slug
     * @param Center $center
     * @param CenterRepository $centerRepository
     * @return Response
     */
    public function DisplayCenter($id, $slug, Center $center, CenterRepository $centerRepository)
    {

        return $this->render('front/centers/center.html.twig', [
            'controller_name' => 'FrontControllerDisplayCenter',
            'centers' => $centerRepository->findPublishedCenters(),
            'center_active' => $center
        ]);
    }

    /**
     * @Route("/le-jeu", name="game")
     * @param CenterRepository $centerRepository
     * @return Response
     */
    public function DisplayJeu(CenterRepository $centerRepository){

        return $this->render('front/game/game_rules.html.twig', [
            'controller_name' => 'FrontControllerDisplayJeu',
            'centers' => $centerRepository->findPublishedCenters()
        ]);
    }

    /**
     * @Route("/les-points", name="game_points")
     * @param CenterRepository $centerRepository
     * @return Response
     */
    public function showGamePoints(CenterRepository $centerRepository){

        return $this->render('front/game/game_points.html.twig', [
            'centers' => $centerRepository->findPublishedCenters()
        ]);
    }

    /**
     * @route("/shinigamilaserclubs", name="shinigamilaserclubs")
     * @param CenterRepository $centerRepository
     * @return Response
     */
    public function DisplayCenters(CenterRepository $centerRepository){
        return $this->render('front/centers/centers_list.html.twig', [
            'controller_name' => 'FrontController',
            'centers' => $centerRepository->findPublishedCenters()
        ]);
    }
    
}