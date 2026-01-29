<?php

declare(strict_types=1);

namespace Continuum\Dto\Request\User;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class EditLocation
{
    public const int LATITUDE_MIN = -90;
    public const int LATITUDE_MAX = 90;
    public const int LONGITUDE_MIN = -180;
    public const int LONGITUDE_MAX = 180;

    public function __construct(
        #[Assert\Range(min: self::LATITUDE_MIN, max: self::LATITUDE_MAX)]
        public float $latitude,
        #[Assert\Range(min: self::LONGITUDE_MIN, max: self::LONGITUDE_MAX)]
        public float $longitude,
    ) {}
}
