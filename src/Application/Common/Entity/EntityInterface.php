<?php

declare(strict_types=1);

namespace App\Application\Common\Entity;

use Symfony\Component\Uid\Ulid;

interface EntityInterface
{
    public function getId(): ?Ulid;

    public function setId(Ulid $id): static;

    public function setDefaultId(): static;

    public function getCreatedAt(): ?\DateTimeInterface;

    public function setCreatedAt(\DateTimeInterface $createdAt): static;

    public function getUpdatedAt(): ?\DateTimeInterface;

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static;
}
