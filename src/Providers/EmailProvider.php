<?php

namespace App\Providers;

use App\Interface\IProvider;
use Exception;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

readonly class EmailProvider implements IProvider
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    /**
     * @throws Exception
     */
    public function send(string $notification, string $contact): void
    {
        $this->process($contact, $notification);
    }

    /**
     * @throws Exception
     */
    private function process(string $contact, string $notification): void
    {
        try {
            $email = (new Email())
                ->from('fin-tech@example.com')
                ->to($contact)
                ->subject('Important')
                ->text($notification);

            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
