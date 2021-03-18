<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @return Response
     */
    public function homepage(): Response
    {
        return new Response('Bienvenue sur la page d\'accueil !');
    }
}