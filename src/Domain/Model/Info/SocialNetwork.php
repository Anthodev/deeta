<?php

declare(strict_types=1);

namespace App\Domain\Model\Info;

use App\Domain\Model\Common\ModelInterface;
use App\Domain\Model\User\User;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Uid\Ulid;

class SocialNetwork implements ModelInterface
{
    private ?string $id = null;
    private ?\DateTime $createdAt = null;
    private ?\DateTime $updatedAt = null;

    public function __construct(
        private string $label,
        private string $url,
        private string $network,
        private User $user,
        private ?string $defaultColor = null,
        private ?int $position = 0,
    ) {
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function setNetwork(string $network): self
    {
        $this->network = $network;

        return $this;
    }

    public function getDefaultColor(): ?string
    {
        return $this->defaultColor;
    }

    public function setDefaultColor(?string $defaultColor): self
    {
        $this->defaultColor = $defaultColor;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[Ignore]
    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    #[Ignore]
    public function setDefaultId(): self
    {
        $this->id = new Ulid()->toRfc4122();

        return $this;
    }
}
