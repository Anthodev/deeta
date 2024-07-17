<?php

declare(strict_types=1);

namespace App\Application\Common\Entity\Trait;

use App\Application\Common\Enum\SerializerGroupNameEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Uid\Ulid;

trait IdTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(name: 'id', type: UlidType::NAME, unique: true)]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    #[Groups([SerializerGroupNameEnum::DEFAULT_READ->value])]
    private ?Ulid $id = null;

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function setId(Ulid $id): static
    {
        $this->id = $id;

        return $this;
    }

    #[Ignore]
    public function setDefaultId(): static
    {
        $this->id = new Ulid();

        return $this;
    }
}
