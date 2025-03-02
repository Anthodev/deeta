<?php

declare(strict_types=1);

namespace App\Domain\Model\User;

use App\Domain\Model\Common\ModelInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Ulid;

class User implements ModelInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    private ?string $id = null;
    private ?\DateTime $createdAt = null;
    private ?\DateTime $updatedAt = null;

    public ?string $fullName {
        get => $this->firstName.' '. $this->lastName;
    }

    public function __construct(
        private string $email,
        private string $username,
        private ?string $firstName = null,
        private ?string $lastName = null,
        private ?string $jobTitle = null,
        private ?string $company = null,
        private ?string $location = null,
        private ?string $password = null,
        private ?string $plainPassword = null,
        private int $enabled = 0,
        private ?Role $role = null,
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(?string $jobTitle): self
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function isEnabled(): int
    {
        return $this->enabled;
    }

    public function setEnabled(int $enabled): self
    {
        $this->enabled = $enabled;

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

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function setDefaultId(): self
    {
        $this->id = new Ulid()->toRfc4122();

        return $this;
    }

    public function getRoles(): array
    {
        /** @var Role $role */
        $role = $this->role;
        $roleCode = $role->getCode();

        return [$roleCode];
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        /** @phpstan-ignore-next-line */
        return $this->getEmail();
    }
}
