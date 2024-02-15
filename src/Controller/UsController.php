<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UsController extends AbstractController
{
    #[Route('/cjfms', name: 'app_cjfms')]
    public function cjfms(): Response
    {
        return $this->render('us/cjfms.html.twig', [
            'controller_name' => 'UsController',
        ]);
    }
}
