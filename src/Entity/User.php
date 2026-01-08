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
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\HasLifecycleCallbacks]
final class User implements UserInterface, PasswordAuthenticatedUserInterface
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

    public function activate(): void
    {
        $this->status = UserStatus::Active;
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

    public function visited(): void
    {
        $this->lastVisitedAt = new DateTimeImmutable();
    }

    public function getTimezone(): DateTimeZone
    {
        return new DateTimeZone($this->timezone);
    }

    public function setTimezone(DateTimeZone $timezone): void
    {
        $this->timezone = $timezone->getName();
    }
}
