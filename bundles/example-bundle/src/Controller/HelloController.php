<?php declare(strict_types = 1);
namespace Armin\ExampleBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/hello', name: 'example_hello_')]
class HelloController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('@Example/hello.twig.html');
    }
}
