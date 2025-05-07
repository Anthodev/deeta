<?php

declare(strict_types=1);

namespace App\Presentation\Enum;

enum AdminEventEnum: string
{
    case USER_PROFILE_PICTURE_UPDATED = 'profilePictureUpdated';
    case USER_PROFILE_PICTURE_DELETED = 'profilePictureDeleted';
}
