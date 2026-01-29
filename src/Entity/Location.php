<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final readonly class Location
{
    public function __construct(
        #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 6, options: ['default' => 0])]
        private string $latitude,
        #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 6, options: ['default' => 0])]
        private string $longitude,
    ) {}

    public function getLatitude(): float
    {
        return (float) $this->latitude;
    }

    public function getLongitude(): float
    {
        return (float) $this->longitude;
    }
}
