<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Application\Common\Entity\EntityInterface;
use App\Application\Common\Entity\Trait\IdTrait;
use App\Application\Common\Entity\Trait\TimestampableTrait;
use App\Application\Common\Enum\SerializerGroupNameEnum;
use App\Infrastructure\Persistence\Doctrine\User\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'This email is already used.')]
#[UniqueEntity(fields: ['username'], message: 'This username is already used.')]
#[Gedmo\SoftDeleteable(hardDelete: false)]
class User implements EntityInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    use IdTrait;
    use TimestampableTrait;
    use SoftDeleteableEntity;

    #[Assert\NotBlank, Assert\Email]
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    #[Groups([SerializerGroupNameEnum::DEFAULT_READ->value])]
    private string $email;

    #[Assert\NotBlank, Assert\Length(min: 3, max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    #[Groups([SerializerGroupNameEnum::DEFAULT_READ->value])]
    private string $username;

    #[Ignore]
    #[Assert\Length(min: 12, max: 255, minMessage: 'Your password must be at least {{ limit }} characters long.')]
    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $password;

    #[Ignore]
    #[Assert\Length(min: 12, max: 255, minMessage: 'Your password must be at least {{ limit }} characters long.'), Assert\PasswordStrength]
    private ?string $plainPassword;

    #[Ignore]
    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $enabled = false;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[Groups([SerializerGroupNameEnum::DEFAULT_READ->value])]
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

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

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

    public function getRoles(): array
    {
        /** @var string $roleCode */
        $roleCode = $this->role->getCode();

        return [$roleCode];
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }
}
