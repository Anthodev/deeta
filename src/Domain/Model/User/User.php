<?php

declare(strict_types=1);

namespace App\Domain\Model\User;

use App\Domain\Model\Common\ModelInterface;
use App\Domain\Model\Info\Skill;
use App\Domain\Model\Info\SocialNetwork;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Uid\Ulid;

class User implements ModelInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    private ?string $id = null;
    private ?\DateTime $createdAt = null;
    private ?\DateTime $updatedAt = null;
    /**
     * @var Collection<int, SocialNetwork>
     */
    private Collection $socialNetworks;
    /**
     * @var Collection<int, Skill>
     */
    private Collection $skills;
    private ?string $fullName = null;

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
        private ?string $profileImagePath = null,
        private int $enabled = 0,
        private ?Role $role = null,
    ) {
        $this->socialNetworks = new ArrayCollection();
        $this->skills = new ArrayCollection();
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
        $this->setFullName();

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        $this->setFullName();

        return $this;
    }

    public function getFullName(): ?string
    {
        $this->setFullName();

        return $this->fullName;
    }

    private function setFullName(): self
    {
        if (null !== $this->firstName && null !== $this->lastName) {
            $this->fullName = $this->firstName.' '.$this->lastName;

            return $this;
        }

        if (null !== $this->firstName) {
            $this->fullName = $this->firstName;

            return $this;
        }

        if (null !== $this->lastName) {
            $this->fullName = $this->lastName;

            return $this;
        }

        $this->fullName = null;

        return $this;
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

    public function getProfileImagePath(): ?string
    {
        return $this->profileImagePath;
    }

    public function setProfileImagePath(?string $profileImagePath): self
    {
        $this->profileImagePath = $profileImagePath;

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

    #[Ignore]
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

    /**
     * @return Collection<int, SocialNetwork>
     */
    public function getSocialNetworks(): Collection
    {
        return $this->socialNetworks;
    }

    public function addSocialNetwork(SocialNetwork $socialNetwork): self
    {
        if (!$this->socialNetworks->contains($socialNetwork)) {
            $this->socialNetworks->add($socialNetwork);
        }

        return $this;
    }

    public function removeSocialNetwork(SocialNetwork $socialNetwork): self
    {
        if ($this->socialNetworks->contains($socialNetwork)) {
            $this->socialNetworks->removeElement($socialNetwork);
        }

        return $this;
    }

    /**
     * @return Collection<int, Skill>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
        }

        return $this;
    }

    public function removeSkill(Skill $skill): self
    {
        if ($this->skills->contains($skill)) {
            $this->skills->removeElement($skill);
        }

        return $this;
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
