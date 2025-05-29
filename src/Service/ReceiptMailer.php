<?php




namespace App\Service;

use App\Repository\SaleRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use App\Service\PriceManagementService;
use Symfony\Component\HttpFoundation\RequestStack;
use TCPDF;

class ReceiptMailer
{


    public function __construct(
        private MailerInterface $mailer,
        private PriceManagementService $priceManagement,
        private RequestStack $requestStack,
        private SaleRepository $saleRepository,


    ) {}

    public function sendReceipt(string $to): void
    {
        $totalAmount = $this->priceManagement->getCartTotal();

        $request = $this->requestStack->getCurrentRequest();
        $pwywAmount = $request->get("pwyw_amount");

        if ($pwywAmount) {
            $totalAmount += (float) $pwywAmount;
        }

        $formattedAmount = number_format($totalAmount, 2, ',', '');
        $pdfPath = $this->generateReceiptPdf();


        $email = (new TemplatedEmail())
            ->from('example@examples.com')
            ->to($to)
            ->subject('Votre ticket de caisse - Les Fourmis Soli\'Terre')
            ->htmlTemplate('email/receipt.html.twig')
            ->context([
                'total_amount' => $formattedAmount
            ])
            ->attachFromPath($pdfPath, 'ticket-de-caisse.pdf', 'application/pdf');


        $this->mailer->send($email);
    }


    private function generateReceiptPdf(): string
    {
        $session = $this->requestStack->getSession();
        $shoppingCart = $session->get('shopping_cart', []);

        $totalAmount = $this->priceManagement->getCartTotal($shoppingCart);

        $request = $this->requestStack->getCurrentRequest();
        $pwywAmount = $request->get("pwyw_amount");

        if ($pwywAmount) {
            $totalAmount += (float) $pwywAmount;
        }

        $formattedTotalAmount = number_format($totalAmount, 2, ',', '') . ' €';

        $saleDateTime = new \DateTimeImmutable();
        $createdAt = $saleDateTime->format('d/m/Y H:i');

        $businessName = "Les Fourmis Soli'Terre";
        $address = "36 A Bd de la Gare, 22830 Plouasne";
        $siret = "SIRET: 92151062400015";

        $pdf = new TCPDF();
        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Ticket de caisse', 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 7, $businessName, 0, 1, 'C');
        $pdf->Cell(0, 7, $address, 0, 1, 'C');
        $pdf->Cell(0, 7, $siret, 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, "Date de la transaction : " . $createdAt, 0, 1, 'L');
        $pdf->Ln(5);

        foreach ($shoppingCart as $item) {
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->Cell(0, 7, $item['category'], 0, 1);

            if (!empty($item['quantity'])) {
                $pdf->SetFont('Helvetica', '', 12);
                $pdf->Cell(0, 5, 'Quantité : ' . $item['quantity'], 0, 1);
            } elseif (!empty($item['weight'])) {
                $pdf->SetFont('Helvetica', '', 12);
                $pdf->Cell(0, 5, 'Poids : ' . $item['weight'] . ' kg', 0, 1);
            }
            $pdf->Cell(0, 5, 'Prix unitaire : ' . number_format($item['price'], 2, ',', '') . ' €', 0, 1);
            $pdf->Ln(5);
        }

        if ($pwywAmount) {
            $pdf->SetFont('helvetica', 'I', 12);
            $pdf->writeHTML("<i>Vous avez acheté des articles en vrac pesant moins de 1 kg. Conformément à notre politique, vous avez réglé en prix libre : <b>" . number_format($pwywAmount, 2, ',', '') . " €</b>.</i>");
            $pdf->Ln(10);
        }

        $pdf->SetFont('Helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Total : ' . $formattedTotalAmount, 0, 1, 'L');
        $pdf->Ln(10);

        $filepath = sys_get_temp_dir() . '/receipt_' . uniqid() . '.pdf';
        $pdf->Output($filepath, 'F');

        return $filepath;
    }
}
