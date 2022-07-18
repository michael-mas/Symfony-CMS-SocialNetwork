<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RgpdController extends AbstractController
{
    #[Route('/rgpd', name: 'mentions.rgpd')]
    public function index(): Response
    {
        return $this->render('rgpd/index.html.twig', [
            'controller_name' => 'RgpdController',
        ]);
    }

    #[Route('/contact_me', name: 'contact.rgpd')]
    public function contact(): Response
    {
        return $this->render('rgpd/contact.html.twig', [
            'controller_name' => 'RgpdController',
        ]);
    }
}
