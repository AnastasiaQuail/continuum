<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Repository\UserRepository;
use Continuum\Security\User\UserRole;
use Continuum\Security\User\UserStatus;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Firewall\ContextListener;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
#[ORM\UniqueConstraint(name: 'UNIQ_USERS_EMAIL', fields: ['email'])]
#[ORM\HasLifecycleCallbacks]
final class User implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public private(set) Uuid $id;

    #[ORM\Column]
    public string $password {
        get => $this->password ?? throw new InvalidArgumentException('Password should be set.');
        set => '' !== $value ? $value : throw new InvalidArgumentException('Password cannot be empty.');
    }

    #[ORM\Column(enumType: UserStatus::class)]
    public UserStatus $status = UserStatus::Created;

    /**
     * @var list<non-empty-string>
     */
    #[ORM\Column]
    public private(set) array $roles = [] {
        // guarantee every user at least has ROLE_USER
        get => array_values(array_unique(array_merge($this->roles, [UserRole::User->value])));
        set => array_values(array_unique($value));
    }

    #[ORM\Column]
    public private(set) DateTimeImmutable $createdAt;

    #[ORM\Column]
    public private(set) DateTimeImmutable $updatedAt;

    #[ORM\Column]
    public private(set) DateTimeImmutable $lastVisitedAt;

    public DateTimeZone $timezone {
        get => new DateTimeZone($this->timezoneName);
        set(DateTimeZone $timezone) {
            $this->timezoneName = $timezone->getName();
        }
    }

    #[ORM\Embedded(columnPrefix: false)]
    public Location $location;

    #[ORM\Column(name: 'timezone', length: 64)]
    private string $timezoneName;

    public function __construct(
        #[ORM\Column(length: 180)]
        public private(set) string $email {
            set => '' !== $value ? $value : throw new InvalidArgumentException('Email cannot be empty.');
        }
    ) {
        $this->id = Uuid::v7();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = $this->createdAt;
        $this->lastVisitedAt = $this->createdAt;
        $this->timezoneName = date_default_timezone_get();
        $this->location = new Location('0.0', '0.0');
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    /**
     * @see ContextListener
     */
    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        if (
            $user->email !== $this->email
            || $user->status !== $this->status
        ) {
            return false;
        }

        $otherPassword = $user->password;
        $password = $this->password;

        if (
            $otherPassword !== $password
            && (
                8 !== strlen($password)
                || hash('crc32c', $otherPassword) !== $password
            )
        ) {
            return false;
        }

        $otherRoles = $user->roles;
        $roles = $this->roles;

        return (count($otherRoles) === count($roles))
            && (count($otherRoles) === count(array_intersect($otherRoles, $roles)));
    }

    /**
     * @return non-empty-string
     */
    public function getUserIdentifier(): string
    {
        assert('' !== $this->email);

        return $this->email;
    }

    /**
     * @return non-empty-string
     */
    public function getPassword(): string
    {
        assert('' !== $this->password);

        return $this->password;
    }

    /**
     * @return list<non-empty-string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function addRole(UserRole $role): void
    {
        $this->roles = array_merge($this->roles, [$role->value]);
    }

    #[ORM\PreUpdate]
    public function update(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
