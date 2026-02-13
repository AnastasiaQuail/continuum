<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Repository\UserRepository;
use Continuum\Security\User\UserRole;
use Continuum\Security\User\UserStatus;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
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
    private Uuid $id;

    /**
     * @var non-empty-string
     */
    #[ORM\Column]
    private string $password = '';

    #[ORM\Column(enumType: UserStatus::class)]
    private UserStatus $status = UserStatus::Active;

    /**
     * @var list<non-empty-string>
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column]
    private ?DateTimeImmutable $lastVisitedAt;

    #[ORM\Column(length: 64)]
    private string $timezone;

    #[ORM\Embedded(class: Location::class, columnPrefix: false)]
    private Location $location;

    public function __construct(
        /**
         * @var non-empty-string
         */
        #[ORM\Column(length: 180)]
        private readonly string $email,
    ) {
        $this->id = Uuid::v7();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->lastVisitedAt = new DateTimeImmutable();
        $this->timezone = date_default_timezone_get();
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

        $otherPassword = $user->getPassword();
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

        $otherRoles = $user->getRoles();
        $roles = $this->getRoles();

        if (
            count($otherRoles) !== count($roles)
            || count($otherRoles) !== count(array_intersect($otherRoles, $roles))
        ) {
            return false;
        }

        if ($this->email !== $user->getUserIdentifier()) {
            return false;
        }

        return $this->status === $user->getStatus();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return non-empty-string
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return non-empty-string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param non-empty-string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function setStatus(UserStatus $status): void
    {
        $this->status = $status;
    }

    public function isActive(): bool
    {
        return UserStatus::Active === $this->status;
    }

    public function isDisabled(): bool
    {
        return UserStatus::Disabled === $this->status;
    }

    /**
     * @return list<non-empty-string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = UserRole::User->value;

        return array_unique($roles);
    }

    public function addRole(UserRole $role): void
    {
        $this->roles[] = $role->value;
    }

    /**
     * @param non-empty-string ...$roles
     */
    public function setRoles(string ...$roles): void
    {
        $this->roles = $roles;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function update(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getLastVisitedAt(): DateTimeImmutable
    {
        return $this->lastVisitedAt;
    }

    public function getTimezone(): DateTimeZone
    {
        return new DateTimeZone($this->timezone);
    }

    public function setTimezone(DateTimeZone $timezone): void
    {
        $this->timezone = $timezone->getName();
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): void
    {
        $this->location = $location;
    }
}
