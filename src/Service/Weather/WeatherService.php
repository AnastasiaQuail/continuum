<?php

declare(strict_types=1);

namespace Continuum\Service\Weather;

use Continuum\Component\Weather\Dto\Weather;
use Continuum\Component\Weather\OpenMeteoClient;
use Continuum\Entity\Location;

final readonly class WeatherService
{
    public function __construct(
        private OpenMeteoClient $client,
        private WeatherCache $cache,
    ) {}

    public function getWeather(Location $location): Weather
    {
        $weather = $this->cache->get($location);

        if (null === $weather) {
            $weather = $this->client->getCurrent($location->latitude, $location->longitude);

            $this->cache->save($location, $weather);
        }

        return $weather;
    }
}
