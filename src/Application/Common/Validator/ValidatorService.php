<?php

declare(strict_types=1);

namespace App\Application\Common\Validator;

use App\Application\Common\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class ValidatorService
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    public function validate(object $object): void
    {
        $violations = $this->validator->validate($object);

        if (count($violations) > 0) {
            throw new ValidationException((string) $violations);
        }
    }
}
