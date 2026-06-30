<?php

declare(strict_types=1);

namespace Continuum\Component\Weather;

use Continuum\Component\Weather\Dto\Weather;
use Continuum\Component\Weather\Dto\Wind;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @see https://open-meteo.com/en/docs
 */
final readonly class OpenMeteoClient
{
    private const string API_URL = 'https://api.open-meteo.com';

    public function __construct(
        private HttpClientInterface $client,
    ) {}

    public function getCurrent(float $latitude, float $longitude): Weather
    {
        try {
            /**
             * @var array{
             *  latitude: float,
             *  longitude: float,
             *  generationtime_ms: float,
             *  utc_offset_seconds: int,
             *  timezone: string,
             *  timezone_abbreviation: string,
             *  elevation: float,
             *  current_weather_units: array{
             *      time: string,
             *      interval: string,
             *      temperature: string,
             *      windspeed: string,
             *      winddirection: string,
             *      is_day: string,
             *      weathercode: string
             *  },
             *  current_weather: array{
             *      time: string,
             *      interval: int,
             *      temperature: float,
             *      windspeed: float,
             *      winddirection: int,
             *      is_day: int,
             *      weathercode: int
             *  }
             * } $data
             */
            $data = $this->sendRequest('/v1/forecast', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current_weather' => 'true',
            ]);
        } catch (TransportExceptionInterface) {
            return new Weather(temperature: 0.0);
        }

        return new Weather(
            temperature: $data['current_weather']['temperature'],
            code: WmoCode::tryFrom($data['current_weather']['weathercode']),
            wind: new Wind(
                speed: round($data['current_weather']['windspeed'] / 3.6, 1), // convert km/h to m/s
                direction: WindDirection::fromDegrees($data['current_weather']['winddirection']),
            ),
        );
    }

    /**
     * @param non-empty-string $path
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     *
     * @throws BadRequestHttpException
     * @throws TransportExceptionInterface
     */
    private function sendRequest(string $path, array $params = []): array
    {
        $response = $this->client->request(
            method: 'GET',
            url: self::API_URL . $path . '?' . http_build_query($params),
            options: [
                'timeout' => 5,
                'max_connect_duration' => 1,
            ],
        );

        if (200 !== $response->getStatusCode()) {
            throw new BadRequestHttpException('Something went wrong.');
        }

        /** @var array<string, mixed> */
        return $response->toArray();
    }
}
