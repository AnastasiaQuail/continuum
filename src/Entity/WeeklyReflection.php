<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Continuum\Repository\WeeklyReflectionRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WeeklyReflectionRepository::class)]
#[ORM\Table(name: 'weekly_reflections')]
#[ORM\UniqueConstraint(name: 'UNIQ_WEEKLY_REFLECTIONS_DATE', fields: ['date'])]
final class WeeklyReflection
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public private(set) Uuid $id;

    public function __construct(
        #[ORM\Column(type: Types::DATE_IMMUTABLE)]
        public readonly DateTimeImmutable $date,
        #[ORM\Embedded]
        public TextField $joy,
        #[ORM\Embedded]
        public TextField $difficulty,
        #[ORM\Embedded]
        public TextField $achievement,
    ) {
        $this->id = Uuid::v7();
    }
}
