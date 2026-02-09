<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Dto\Response\CoupleInformation;
use Continuum\Entity\Location;
use Continuum\Entity\User;
use Continuum\Service\Weather\WeatherService;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Uid\Uuid;

final readonly class CoupleService
{
    public function __construct(
        #[Autowire(env: 'APP_USER_ID')]
        private string $userId,
        #[Autowire(env: 'APP_PARTNER_USER_ID')]
        private string $partnerUserId,
        private UserService $userService,
        private WeatherService $weatherService,
        #[Autowire(env: 'APP_PARTNER_DATE_START')]
        private string $startDate,
    ) {}

    public function getInformation(User $currentUser): CoupleInformation
    {
        $user = $this->userService->get(Uuid::fromString($this->userId));
        $partnerUser = $this->userService->get(Uuid::fromString($this->partnerUserId));

        $userWeather = $this->weatherService->getWeather($user->getLocation());
        $partnerUserWeather = $this->weatherService->getWeather($partnerUser->getLocation());

        $startDate = new DateTimeImmutable($this->startDate, $currentUser->getTimezone());
        $currentDate = new DateTimeImmutable('now', $currentUser->getTimezone());

        return new CoupleInformation(
            weather: $userWeather,
            time: new DateTimeImmutable('now', $user->getTimezone()),
            partnerWeather: $partnerUserWeather,
            partnerTime: new DateTimeImmutable('now', $partnerUser->getTimezone()),
            distance: $this->getDistance($user->getLocation(), $partnerUser->getLocation()),
            together: $startDate->diff($currentDate),
        );
    }

    private function getDistance(Location $location, Location $partnerLocation): float
    {
        $a = sin((deg2rad($partnerLocation->getLatitude()) - deg2rad($location->getLatitude())) / 2) ** 2 +
            cos(deg2rad($partnerLocation->getLatitude())) * cos(deg2rad($location->getLatitude())) *
            sin((deg2rad($partnerLocation->getLongitude()) - deg2rad($location->getLongitude())) / 2) ** 2;

        return 6371 * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
