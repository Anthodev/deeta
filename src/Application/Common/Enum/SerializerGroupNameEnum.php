<?php

declare(strict_types=1);

namespace App\Application\Common\Enum;

enum SerializerGroupNameEnum: string
{
    case DEFAULT_READ = 'default:read';
    case DEFAULT_WRITE = 'default:write';
}
