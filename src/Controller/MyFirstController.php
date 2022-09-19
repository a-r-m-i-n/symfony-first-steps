<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyFirstController extends AbstractController
{
    #[Route('/', name: 'app_welcome')]
    public function welcome(): Response
    {
        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'MyFirstController',
        ]);
    }
}
