<?php

declare(strict_types=1);

namespace App\Application\Common\Entity\Trait;

use App\Application\Common\Enum\SerializerGroupNameEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait CodeTrait
{
    #[Assert\NotBlank, Assert\Length(min: 3, max: 32)]
    #[ORM\Column(type: Types::STRING, length: 32, unique: true)]
    #[Groups([SerializerGroupNameEnum::DEFAULT_READ->value])]
    private string $code;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }
}
