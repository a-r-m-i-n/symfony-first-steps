<?php

namespace App\Controller;

use App\Service\RandomNumberGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MyFirstController extends AbstractController
{
    #[Route('/', name: 'app_welcome')]
    public function welcome(RandomNumberGeneratorService $randomNumberGeneratorService, ParameterBagInterface $parameterBag): Response
    {
        $absoluteUrlToRoute = $this->generateUrl(
            'app_pizza_essen',
            ['sorte' => 'Quattro Formaggi'],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        dump('Max number in parameter bag is: ' . $parameterBag->get('app.max_number'));

        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'MyFirstController',
            'absolute_url_to_route' => $absoluteUrlToRoute,
            'random_number' => $randomNumberGeneratorService->generate(),
            'configuredParameters' => $parameterBag->all(),
        ]);
    }
}
