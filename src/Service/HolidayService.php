<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Entity\User;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class HolidayService
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/data/holidays.php')]
        private string $holidaysFilename,
    ) {}

    /**
     * @return list<non-empty-string>
     */
    public function getHolidays(DateTimeImmutable $date): array
    {
        /** @var non-empty-array<non-empty-string, list<non-empty-string>> $holidays */
        $holidays = include $this->holidaysFilename;

        return $holidays[$date->format('m-d')] ?? [];
    }

    /**
     * @return list<non-empty-string>
     */
    public function getTodayHolidays(User $user): array
    {
        $date = new DateTimeImmutable('now', $user->timezone);

        return $this->getHolidays($date);
    }
}
