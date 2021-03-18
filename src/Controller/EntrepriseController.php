<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EntrepriseController
 * @package App\Controller
 * @Route("/entreprise")
 */
class EntrepriseController extends AbstractController
{
    /**
     * @Route("/")
     * @return Response
     */
    public function temp(): Response
    {
        return new Response('Vous êtes dans la partie <strong>Entreprise</strong>');
    }
}