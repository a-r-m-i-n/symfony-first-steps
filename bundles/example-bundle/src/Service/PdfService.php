<?php declare(strict_types = 1);
namespace Armin\ExampleBundle\Service;

use App\Kernel;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;

class PdfService
{
    private Environment $twig;
    private string $mode;
    private string $format;
    private string $orientation;
    private int $marginLeft;
    private int $marginRight;
    private int $marginTop;
    private int $marginBottom;
    private int $marginHeader;
    private int $marginFooter;
    private string $tempDir;
    private Kernel $kernel;

    public function __construct(
        Kernel $kernel,
        Environment $twig,
        string $mode = 'utf-8',
        string $format = 'A4',
        string $orientation = 'P',
        int $marginLeft = 15,
        int $marginRight = 15,
        int $marginTop = 28,
        int $marginBottom = 32,
        int $marginHeader = 9,
        int $marginFooter = 9,
        ?string $tempDir = null
    ) {
        $this->kernel = $kernel;
        $this->twig = $twig;
        $this->mode = $mode;
        $this->format = $format;
        $this->orientation = $orientation;
        $this->marginLeft = $marginLeft;
        $this->marginRight = $marginRight;
        $this->marginTop = $marginTop;
        $this->marginBottom = $marginBottom;
        $this->marginHeader = $marginHeader;
        $this->marginFooter = $marginFooter;
        $this->tempDir = $tempDir ?? $kernel->getCacheDir();
    }

    public function makePdf(string $template, array $context = []): Mpdf
    {
        $mpdf = $this->getMpdfInstance($context);

        $html = $this->twig->render($template, $context);
        $mpdf->WriteHTML($html);

        return $mpdf;
    }

    public function createBinaryPdfResponse(Mpdf $mpdf): Response
    {
        $binaryPdfContent = $mpdf->Output('', Destination::STRING_RETURN);
        return new Response(
            $binaryPdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Length' => strlen($binaryPdfContent),
                'Content-Disposition' => sprintf('inline; filename="%s"', 'pdf-export.pdf'),
            ]
        );
    }

    private function getMpdfInstance(array $context = []): Mpdf
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'mode' => $this->mode,
            'format' => $this->format,
            'margin_left' => $this->marginLeft,
            'margin_right' => $this->marginRight,
            'margin_top' => $this->marginTop,
            'margin_bottom' => $this->marginBottom,
            'margin_header' => $this->marginHeader,
            'margin_footer' => $this->marginFooter,
            'orientation' => $this->orientation,
            'tempDir' => $this->tempDir,
//            'fontDir' => array_merge($fontDirs, [
//                $this->kernel->getProjectDir() . '/assets/fonts',
//            ]),
//            'fontdata' => $fontData + [
//                    'opensans' => [
//                        'R' => 'OpenSans-Regular.ttf',
//                        'I' => 'OpenSans-Italic.ttf',
//                        'B' => 'OpenSans-SemiBold.ttf',
//                        'BI' => 'OpenSans-SemiBoldItalic.ttf',
//                    ],
//                ],
        ]);

        $variables = array_merge($context, ['project_dir' => $this->kernel->getProjectDir()]);

        try {
            $headerHtml = trim($this->twig->render('@Example/pdf/includes/header.html.twig', $variables));
            if (!empty($headerHtml)) {
                $mpdf->SetHTMLHeader($headerHtml);
            }
        } catch (LoaderError $e) {
        }

        try {
            $footerHtml = trim($this->twig->render('@Example/pdf/includes/footer.html.twig', $variables));
            if (!empty($footerHtml)) {
                $mpdf->SetHTMLFooter($footerHtml);
            }
        } catch (LoaderError $e) {
        }

        return $mpdf;
    }
}
