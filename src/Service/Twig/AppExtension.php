<?php

namespace App\Service\Twig;

use Behat\Transliterator\Transliterator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{

    private $em;
    private $session;


    /**
     * AppExtension constructor.
     * @param EntityManagerInterface $manager
     * @param TokenStorageInterface $tokenStorage
     * @param SessionInterface $session
     */
    public function __construct(EntityManagerInterface $manager,
                                TokenStorageInterface $tokenStorage,
                                SessionInterface $session)
    {
        # Récupération de Doctrine
        $this->em = $manager;

        # Récupération de la session
        $this->session = $session;


    }

    public function getFilters()
    {
        return [
          new \Twig_Filter('slugify', function($text) {


              $string = Transliterator::transliterate($text);
              // $string = $this->slugify($text);

              return $string;

          },['is_safe' => ['html']]),
            new \Twig_Filter('ksort', function($array) {
                ksort($array);
                return $array;
            })
        ];
    }

}