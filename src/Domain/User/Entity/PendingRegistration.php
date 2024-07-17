<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Application\Common\Entity\EntityInterface;
use App\Application\Common\Entity\Trait\IdTrait;
use App\Application\Common\Entity\Trait\TimestampableTrait;
use App\Domain\User\Validator as UserValidator;
use App\Infrastructure\Persistence\Doctrine\User\Repository\PendingRegistrationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PendingRegistrationRepository::class)]
#[ORM\Table(name: 'pending_registrations')]
#[ORM\HasLifecycleCallbacks]
#[UserValidator\PendingRegistrationNotExists]
class PendingRegistration implements EntityInterface, PasswordAuthenticatedUserInterface
{
    use IdTrait;
    use TimestampableTrait;

    #[Assert\NotBlank, Assert\Email]
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $email;

    #[Assert\NotBlank, Assert\Length(min: 3, max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $username;

    #[Assert\Length(min: 12, max: 255, minMessage: 'Your password must be at least {{ limit }} characters long.')]
    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $password;

    #[Assert\Length(min: 12, max: 255, minMessage: 'Your password must be at least {{ limit }} characters long.'), Assert\PasswordStrength]
    private ?string $plainPassword;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $token;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTime $expiresAt;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    private Role $role;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getExpiresAt(): \DateTime
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTime $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }
}
