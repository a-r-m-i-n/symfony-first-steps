<?php declare(strict_types = 1);
namespace Armin\ExampleBundle\Controller;


use App\Dto\CarrierFilterDto;
use Armin\ExampleBundle\Repository\CarrierRepository;
use Armin\ExampleBundle\Service\PdfService;
use Mpdf\Output\Destination;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/hello', name: 'example_hello_')]
class HelloController extends AbstractController
{
    private int $itemsPerPage;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->itemsPerPage = $parameterBag->get('pagination.itemsPerPage');
    }

    #[Route('/{page}', name: 'index', defaults: ['page' => 1], requirements: ['page' => '\d+'], methods: ['GET', 'POST'])]
    public function index(Request $request, CarrierRepository $carrierRepository, int $page = 1): Response
    {
        $filter = $request->getSession()->get(CarrierFilterDto::class, new CarrierFilterDto());

        $resetUrl = $this->generateUrl('example_hello_reset');
        $form = $this->createFormBuilder($filter)
            ->add('query', TextType::class, ['required' => false])
            ->add('isCool', ChoiceType::class, ['required' => false, 'empty_data' => null, 'choices' => [
                'Yes' => true,
                'No' => false,
            ]])
            ->add('submit', SubmitType::class, ['label' => 'Suchen'])

            ->add('reset', ButtonType::class, ['label' => 'Reset', 'attr' => [
                'onclick' => 'location.href = "' . $resetUrl . '";'
            ]])

            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $request->getSession()->set(CarrierFilterDto::class, $form->getData());
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

    #[Route('/reset-session', name: 'reset')]
    public function reset(Request $request)
    {
        $request->getSession()->remove(CarrierFilterDto::class);

        return $this->redirectToRoute('example_hello_index');
    }

    #[Route('/export/pdf', name: 'export_pdf')]
    public function exportPdf(
        Request $request,
        CarrierRepository $carrierRepository,
        PdfService $pdfService,
        MailerInterface $mailer
    ): Response {
        $filter = $request->getSession()->get(CarrierFilterDto::class, new CarrierFilterDto());
        $page = (int)$request->get('page', 1);
        if (isset($filter)) {
            $paginator = $carrierRepository->findByFilterDtoPaginated(
                $filter,
                $page,
                $this->itemsPerPage
            );
        } else {
            $paginator = $carrierRepository->findAllPaginated(
                $page,
                $this->itemsPerPage
            );
        }

        $mpdf = $pdfService->makePdf('@Example/pdf/CarrierExport.html.twig', ['items' => $paginator]);

        $email = new Email();
        $email
            ->from('weihnachtsmann@nordpol.gov')
            ->to('armin@v.ieweg.de')
            ->subject('Dein Export als PDF')
            ->text('Hier ist dein PDF')
            ->html('Hier ist <b>dein PDF</b>')
            ->attach($mpdf->Output('', Destination::STRING_RETURN), 'export-attachment.pdf', 'application/pdf')
        ;
        $mailer->send($email);

        return $pdfService->createBinaryPdfResponse($mpdf);
    }
}
