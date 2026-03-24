<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Repository\MessageRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'messages')]
final class Message
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public private(set) Uuid $id;

    #[ORM\Column]
    public private(set) DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    public ?DateTimeImmutable $readAt = null;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'messages')]
        #[ORM\JoinColumn(nullable: false)]
        public private(set) readonly Chat $chat,
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        public private(set) readonly User $sender,
        #[ORM\Column(type: Types::TEXT)]
        public private(set) string $content {
            set => '' !== $value ? $value : throw new InvalidArgumentException('Content cannot be empty.');
        },
    ) {
        $this->id = Uuid::v7();
        $this->createdAt = new DateTimeImmutable();
    }
}
