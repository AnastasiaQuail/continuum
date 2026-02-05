<?php

declare(strict_types=1);

namespace Continuum\Component\Weather;

use Continuum\Component\Weather\Dto\Weather;
use Continuum\Component\Weather\Dto\Wind;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class OpenMeteoClient
{
    private const string API_URL = 'https://api.open-meteo.com';

    public function __construct(
        private HttpClientInterface $client,
    ) {}

    public function getCurrent(float $latitude, float $longitude): Weather
    {
        $data = $this->sendRequest('/v1/forecast', [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'current_weather' => 'true',
        ]);

        return new Weather(
            temperature: (float) $data['current_weather']['temperature'],
            code: WmoCode::tryFrom((int) $data['current_weather']['weathercode']),
            wind: new Wind(
                speed: (float) $data['current_weather']['windspeed'],
                direction: (int) $data['current_weather']['winddirection'],
            ),
        );
    }

    /**
     * @param non-empty-string $path
     * @param array<string, mixed> $params
     */
    public function sendRequest(string $path, array $params = []): array
    {
        $response = $this->client->request('GET', self::API_URL . $path . '?' . http_build_query($params));

        if (200 !== $response->getStatusCode()) {
            throw new BadRequestHttpException('Something went wrong.');
        }

        return $response->toArray();
    }
}
