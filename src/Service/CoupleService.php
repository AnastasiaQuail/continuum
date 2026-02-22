<?php

declare(strict_types=1);

namespace Continuum\Service;

use Continuum\Dto\Response\CoupleInformation;
use Continuum\Dto\Response\CoupleTogetherInformation;
use Continuum\Entity\User;
use Continuum\Service\Weather\WeatherService;
use DateTimeImmutable;
use LogicException;
use Symfony\Bundle\SecurityBundle\Security;
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
        private Security $security,
    ) {}

    public function getInformation(): CoupleInformation
    {
        $user = $this->userService->get(Uuid::fromString($this->userId));
        $partnerUser = $this->userService->get(Uuid::fromString($this->partnerUserId));

        $userWeather = $this->weatherService->getWeather($user->location);
        $partnerUserWeather = $this->weatherService->getWeather($partnerUser->location);

        return new CoupleInformation(
            weather: $userWeather,
            time: new DateTimeImmutable('now', $user->timezone),
            partnerWeather: $partnerUserWeather,
            partnerTime: new DateTimeImmutable('now', $partnerUser->timezone),
            together: $this->getTogether(),
            distance: $user->location->getDistance($partnerUser->location),
        );
    }

    public function getTogether(?DateTimeImmutable $date = null): CoupleTogetherInformation
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new LogicException('User must be authenticated.');
        }

        $date ??= new DateTimeImmutable('now', $user->timezone);

        return new CoupleTogetherInformation(
            new DateTimeImmutable($this->startDate, $user->timezone)->diff($date->setTime(0, 0))
        );
    }
}
