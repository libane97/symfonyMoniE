<?php
namespace App\Services\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    
    private $mailer;
    
    public function __construct(MailerInterface $mailer)
    {
       $this->mailer  = $mailer;
    }

    public function sendEmailCustomer($to)
    {
        $email = (new Email())
            ->from('libanehassan23@gmail.com')
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Djib-Shop')
            ->text('Votre commande à été bien enregistre !');
            $this->mailer->send($email);
    }
}

?>