<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\User\Service;

use App\Domain\User\Factory\PendingRegistrationFactory;
use App\Domain\User\Service\UserMailer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\RawMessage;
use Twig\Environment;
use function Pest\Faker\fake;

it('should send a confirmation email', function () {
    // Given
    $mailer = $this->createMock(MailerInterface::class);

    $pendingRegistration = PendingRegistrationFactory::makePendingRegistration(
        fake()->email(),
        fake()->userName(),
        fake()->password(16),
    );

    $token = md5($pendingRegistration->getEmail());
    $pendingRegistration->setToken($token);

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

    // Then
    $mailer
        ->expects(self::once())
        ->method('send')
        ->with($email)
    ;

    $userMail = new UserMailer(
        $mailer,
    );

    // When
    $userMail->sendConfirmationEmail($pendingRegistration);
});

it('should have correct url in the email', function () {
    // Given
    $mailer = $this->getContainer()->get(MailerInterface::class);
    $twig = $this->getContainer()->get(Environment::class);
    /** @var InMemoryTransport $messageBus */
    $messageBus = $this->getContainer()->get('messenger.transport.async');

    $email = fake()->email();

    $pendingRegistration = PendingRegistrationFactory::makePendingRegistration(
        $email,
        fake()->userName(),
        fake()->password(16),
    );

    $token = md5($pendingRegistration->getEmail());
    $pendingRegistration->setToken($token);

    $userMail = new UserMailer(
        $mailer,
    );

    // When
    $userMail->sendConfirmationEmail($pendingRegistration);

    // Then
    expect($messageBus->get())
        ->toHaveCount(1);

    /** @var RawMessage $email */
    $emailContent = $this->getMailerMessage(0);
    expect($emailContent)
        ->toBeInstanceOf(RawMessage::class)
        ->and($emailContent->toString())
            ->toContain(sprintf('Hello %s', $pendingRegistration->getUsername()))
    ;

    $emailContent = $twig->render('email/confirm.html.twig', [
        'url' => sprintf('https://astral-planner.io/api/register/confirm/%s', $pendingRegistration->getToken()),
        'username' => $pendingRegistration->getUsername(),
    ]);

    expect($emailContent)
        ->toContain(sprintf('Hello %s, welcome on Astral Planner!', $pendingRegistration->getUsername()))
        ->toContain(sprintf('https://astral-planner.io/api/register/confirm/%s', $pendingRegistration->getToken()))
    ;
});
