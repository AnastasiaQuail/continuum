<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Repository\ChatRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ChatRepository::class)]
final class Chat
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public private(set) Uuid $id;

    #[ORM\Column]
    public DateTimeImmutable $lastMessageAt;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'chat', orphanRemoval: true)]
    public private(set) Collection $messages;

    public function __construct(
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        public private(set) readonly User $user1,
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        public private(set) readonly User $user2,
    ) {
        $this->id = Uuid::v7();
        $this->lastMessageAt = new DateTimeImmutable('-1 week');
        $this->messages = new ArrayCollection();
    }
}
