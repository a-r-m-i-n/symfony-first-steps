<?php declare(strict_types = 1);
namespace Armin\ExampleBundle\Controller;


use App\Dto\CarrierFilterDto;
use Armin\ExampleBundle\Repository\CarrierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/{page}', name: 'index', defaults: ['page' => 1], methods: ['GET', 'POST'])]
    public function index(Request $request, CarrierRepository $carrierRepository, int $page = 1): Response
    {
        $filter = new CarrierFilterDto();
        $form = $this->createFormBuilder($filter)
            ->add('query', TextType::class, ['required' => false])
            ->add('isCool', ChoiceType::class, ['required' => false, 'empty_data' => null, 'choices' => [
                'Yes' => true,
                'No' => false,
            ]])
            ->add('submit', SubmitType::class, ['label' => 'Suchen'])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $paginator = $carrierRepository->findByFilterDtoPaginated(
                $form->getData(),
                $page,
                $this->itemsPerPage
            );
        } else {
            $paginator = $carrierRepository->findAllPaginated(
                $page,
                $this->itemsPerPage
            );
        }

        return $this->render('@Example/hello.html.twig', [
            'carriers' => $paginator,
            'thisPage' => $page,
            'maxPages' => (int)ceil($paginator->count() / $this->itemsPerPage),
            'form' => $form->createView(),
        ]);
    }
}
