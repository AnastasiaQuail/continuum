<?php

declare(strict_types=1);

namespace Continuum\Service\Weather;

use Continuum\Component\Weather\Dto\Weather;
use Continuum\Component\Weather\Dto\Wind;
use Continuum\Component\Weather\WmoCode;
use Continuum\Entity\Location;
use DateTimeImmutable;
use Psr\Cache\CacheItemPoolInterface;

final readonly class WeatherCache
{
    public function __construct(
        private CacheItemPoolInterface $cache,
    ) {}

    public function save(Location $location, Weather $weather): void
    {
        $item = $this->cache->getItem($this->getKey($location));
        $item->set([
            'temperature' => $weather->temperature,
            'code' => $weather->code,
            'wind' => [
                'speed' => $weather->wind->speed,
                'direction' => $weather->wind->direction,
            ],
        ]);
        $item->expiresAt(new DateTimeImmutable('+15 minutes'));

        $this->cache->save($item);
    }

    public function get(Location $location): ?Weather
    {
        $item = $this->cache->getItem($this->getKey($location));

        /**
         * @var null|array{temperature: float, code: null|WmoCode, wind: array{speed: float, direction: float}} $data
         */
        $data = $item->get();

        if (!is_array($data)) {
            return null;
        }

        return new Weather(
            temperature: $data['temperature'],
            code: $data['code'],
            wind: new Wind(
                speed: $data['wind']['speed'],
                direction: $data['wind']['direction'],
            ),
        );
    }

    private function getKey(Location $location): string
    {
        return sprintf(
            'app.weather.%s.%s',
            str_replace('.', '_', (string) $location->getLatitude()),
            str_replace('.', '_', (string) $location->getLongitude()),
        );
    }
}
