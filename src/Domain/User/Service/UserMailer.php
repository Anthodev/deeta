<?php

namespace App\Domain\User\Service;

use App\Domain\User\Entity\PendingRegistration;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserMailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendConfirmationEmail(PendingRegistration $pendingRegistration): void
    {
        $senderAddress = new Address('noreply@astral-planner.io', 'Astral Planner');
        $recepientAddress = new Address($pendingRegistration->getEmail(), $pendingRegistration->getUsername());

        $email = (new TemplatedEmail())
            ->to($recepientAddress)
            ->from($senderAddress)
            ->subject('Confirm your registration')
            ->htmlTemplate('email/confirm.html.twig')
            ->context([
                'url' => sprintf('https://astral-planner.io/api/register/confirm/%s', $pendingRegistration->getToken()),
                'username' => $pendingRegistration->getUsername(),
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
