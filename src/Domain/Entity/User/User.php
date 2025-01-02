<?php

declare(strict_types=1);

namespace App\Domain\Entity\User;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use function array_unique;
use function array_values;
use function bin2hex;
use function random_bytes;

// Not ideal that we have infrastructure code here, but we need to use Symfony's UserInterface and PasswordAuthenticatedUserInterface
// to enjoy Symfony's security features
#[ORM\Entity]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /** @var list<string> The user roles */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /** @var string The hashed password */
    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private readonly DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: 'uuid')]
        private ?string $id,
        #[ORM\Column(type: 'string', unique: true)]
        private readonly string $email,
    ) {
        // This generates a tmp password. This way we can create a user object without a password and later set it
        // Symfony password hasher required User object to has a password.
        // Instead we could make the password field nullable, but this way we can avoid null checks,
        // which is bigger benefit in my opinion
        $this->password  = bin2hex(random_bytes(16));
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        if ($this->id === null) {
            throw new LogicException('User id is not set');
        }

        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        /** @var non-empty-string */
        return $this->email;
    }

    /** @return list<string> */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    /** @param list<string> $roles */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function eraseCredentials(): void
    {
    }
}
