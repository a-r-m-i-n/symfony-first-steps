<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pizza', name: 'app_pizza_')]
class PizzaController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $this->addFlash('notice', 'Du wurdest auf "/essen" weitergeleitet. Das nur zur Info ;-)');

        return $this->redirectToRoute('app_pizza_essen');
    }

    #[Route('/essen/{sorte}', name: 'essen')]
    public function essen(string $sorte = 'Margherita'): Response
    {
        return $this->render('pizza/essen.html.twig', [
            'sorte' => ucfirst(trim($sorte)),
        ]);
    }
}
