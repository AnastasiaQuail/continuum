<?php

declare(strict_types=1);

namespace Continuum\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class Location
{
    public function __construct(
        #[ORM\Column(type: Types::FLOAT)]
        public private(set) float $latitude {
            set => round($value, 6);
        },
        #[ORM\Column(type: Types::FLOAT)]
        public private(set) float $longitude {
            set => round($value, 6);
        },
    ) {}

    public function getDistance(self $location): float
    {
        $a = sin((deg2rad($this->latitude) - deg2rad($location->latitude)) / 2) ** 2
            + cos(deg2rad($this->latitude)) * cos(deg2rad($location->latitude))
            * sin((deg2rad($this->longitude) - deg2rad($location->longitude)) / 2) ** 2;

        return 6371 * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
