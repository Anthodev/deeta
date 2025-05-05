<?php

declare(strict_types=1);

namespace App\Tests\Unit\Presentation\Components\Admin;

use App\Application\Command\User\DeleteUserProfilePictureCommand;
use App\Presentation\Components\Admin\AdminProfilePicture;
use App\Presentation\Components\Common\FlashBag;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

beforeEach(function () {
    /** @var MockObject&MessageBusInterface */
    $this->messageBus = $this->createMock(MessageBusInterface::class);

    /** @var MockObject&LoggerInterface */
    $this->logger = $this->createMock(LoggerInterface::class);

    $this->component = new AdminProfilePicture(
        $this->messageBus,
        $this->logger
    );

    /** @var MockObject&FormView */
    $this->formView = $this->createMock(FormView::class);
});

it('mounts with correct properties', function () {
    // Given
    $userId = '01H1234ABCD';
    $username = 'johndoe';
    $profilePicturePath = '/uploads/profile/johndoe.jpg';

    // When
    $this->component->mount(
        $userId,
        $username,
        $this->formView,
        $profilePicturePath
    );

    // Then
    expect($this->component->userId)
        ->toBe($userId)
        ->and($this->component->username)
        ->toBe($username)
        ->and($this->component->profileImageForm)
        ->toBe($this->formView)
        ->and($this->component->profilePicturePath)
        ->toBe($profilePicturePath)
        ->and($this->component->profileDeletedComponentId)
        ->toBeInt()
        ->and($this->component->profileDeletedComponentId)
        ->toBeGreaterThanOrEqual(1)
        ->and($this->component->profileDeletedComponentId)
        ->toBeLessThanOrEqual(100);
});

it('deletes profile picture successfully', function () {
    // Given
    $userId = '01H1234ABCD';
    $this->component->userId = $userId;
    $this->component->profilePicturePath = '/uploads/profile/johndoe.jpg';

    $this->messageBus
        ->expects($this->once())
        ->method('dispatch')
        ->with(
            $this->callback(function ($command) use ($userId) {
                return $command instanceof DeleteUserProfilePictureCommand
                    && $command->getUserId() === $userId;
            })
        )
        ->willReturn(new Envelope(new \stdClass()));

    $this->component = $this->getMockBuilder(AdminProfilePicture::class)
        ->setConstructorArgs([$this->messageBus, $this->logger])
        ->onlyMethods(['emit'])
        ->getMock();

    $this->component->userId = $userId;
    $this->component->profilePicturePath = '/uploads/profile/johndoe.jpg';

    $this->component
        ->expects($this->once())
        ->method('emit')
        ->with(FlashBag::MESSAGE_NEW, [
            'message' => 'Image supprimée',
            'type' => FlashBag::TYPE_SUCCESS,
        ]);

    // When
    $this->component->delete();

    // Then
    expect($this->component->profilePicturePath)
        ->toBeNull()
        ->and($this->component->reloadPage)
        ->toBeTrue();
});

it('handles exception when deleting profile picture', function () {
    // Given
    $userId = '01H1234ABCD';
    $this->component->userId = $userId;

    /** @var MockObject&ExceptionInterface */
    $exception = $this->createMock(ExceptionInterface::class);

    $this->messageBus
        ->expects($this->once())
        ->method('dispatch')
        ->willThrowException($exception);

    $this->logger
        ->expects($this->once())
        ->method('error')
        ->with('Erreur lors de la suppression de l\'image de profil');

    $this->component = $this->getMockBuilder(AdminProfilePicture::class)
        ->setConstructorArgs([$this->messageBus, $this->logger])
        ->onlyMethods(['emit'])
        ->getMock();

    $this->component->userId = $userId;

    $this->component
        ->expects($this->once())
        ->method('emit')
        ->with(FlashBag::MESSAGE_NEW, [
            'message' => 'Erreur lors de la suppression de l\'image',
            'type' => FlashBag::TYPE_ERROR,
        ]);

    // When
    $this->component->delete();
});
