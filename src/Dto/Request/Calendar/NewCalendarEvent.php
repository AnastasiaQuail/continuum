<?php

declare(strict_types=1);

namespace Continuum\Dto\Request\Calendar;

use Continuum\Enum\CalendarEventType;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class NewCalendarEvent
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $title,
        public CalendarEventType $type,
        public ?DateTimeImmutable $time = null,
    ) {}
}
