<?php

declare(strict_types=1);

namespace App\Domain\User\Enum;

enum UserEventEnum: string
{
    case SEND_CONFIRMATION_EMAIL = 'user.send_confirmation_email';
    case USER_CHANGED_PASSWORD = 'user.changed_password';
}
