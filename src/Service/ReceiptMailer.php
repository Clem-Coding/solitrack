<?php




namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use App\Service\PriceManagement;

class ReceiptMailer
{
    // private MailerInterface $mailer;

    public function __construct(private MailerInterface $mailer, private PriceManagement $priceManagement)
    {
        // $this->mailer = $mailer;
    }

    public function sendReceipt(string $to): void
    {

        $totalAmount = $this->priceManagement->getCartTotal();
        $formattedAmount = number_format($totalAmount, 2, ',', '');



        $email = (new TemplatedEmail())
            ->from('example@examples.com')
            ->to($to)
            ->subject('Votre ticket de caisse - Les Fourmis Soli\'Terre')
            ->htmlTemplate('email/receipt.html.twig')
            ->context([
                'total_amount' => $formattedAmount
            ]);


        $this->mailer->send($email);
    }
}
