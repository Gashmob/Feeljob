<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ParticulierController
 * @package App\Controller
 * @Route("/particulier")
 */
class ParticulierController extends AbstractController
{
    /**
     * @Route("/")
     * @return Response
     */
    public function temp(): Response
    {
        return new Response('Vous êtes dans la partie <strong>Particulier</strong>');
    }
}