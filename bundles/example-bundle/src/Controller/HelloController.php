<?php declare(strict_types = 1);
namespace Armin\ExampleBundle\Controller;


use Armin\ExampleBundle\Repository\CarrierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/hello', name: 'example_hello_')]
class HelloController extends AbstractController
{
    private int $itemsPerPage;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->itemsPerPage = $parameterBag->get('pagination.itemsPerPage');
    }

    #[Route('/{page}', name: 'index', defaults: ['page' => 1])]
    public function index(CarrierRepository $carrierRepository, int $page = 1): Response
    {
        return $this->render('@Example/hello.html.twig', [
            'carriers' => $paginator = $carrierRepository->findAllPaginated($page, $this->itemsPerPage),
            'thisPage' => $page,
            'maxPages' => (int)ceil($paginator->count() / $this->itemsPerPage)
        ]);
    }
}
