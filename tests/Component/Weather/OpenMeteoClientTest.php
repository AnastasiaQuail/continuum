<?php

declare(strict_types=1);

namespace Continuum\Tests\Component\Weather;

use Continuum\Component\Weather\OpenMeteoClient;
use Continuum\Component\Weather\WindDirection;
use Continuum\Component\Weather\WmoCode;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\Exception\TimeoutExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[CoversClass(OpenMeteoClient::class)]
final class OpenMeteoClientTest extends TestCase
{
    private HttpClientInterface&MockObject $client;
    private OpenMeteoClient $openMeteoClient;

    #[Override]
    protected function setUp(): void
    {
        $this->client = $this->createMock(HttpClientInterface::class);
        $this->openMeteoClient = new OpenMeteoClient($this->client);
    }

    public function testGetCurrent(): void
    {
        $latitude = 52.52;
        $longitude = 13.405;
        $this->mockRequest(
            params: [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current_weather' => 'true',
            ],
            responseData: [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'generationtime_ms' => 0.123,
                'utc_offset_seconds' => 3600,
                'timezone' => 'Europe/Berlin',
                'timezone_abbreviation' => 'CET',
                'elevation' => 34.0,
                'current_weather_units' => [
                    'time' => 'iso8601',
                    'interval' => 's',
                    'temperature' => '°C',
                    'windspeed' => 'km/h',
                    'winddirection' => '°',
                    'is_day' => '',
                    'weathercode' => 'wmo code',
                ],
                'current_weather' => [
                    'time' => '2025-02-23T12:00',
                    'interval' => 900,
                    'temperature' => 15.5,
                    'windspeed' => 25.0,
                    'winddirection' => 180,
                    'is_day' => 1,
                    'weathercode' => WmoCode::Sunny->value,
                ],
            ]
        );

        $result = $this->openMeteoClient->getCurrent($latitude, $longitude);

        self::assertSame(15.5, $result->temperature);
        self::assertSame(WmoCode::Sunny, $result->code);
        self::assertNotNull($wind = $result->wind);
        self::assertSame(6.9, $wind->speed);
        self::assertSame(WindDirection::South, $wind->direction);
    }

    public function testGetCurrentThrowsExceptionOnHttpError404(): void
    {
        $latitude = 52.52;
        $longitude = 13.405;
        $this->mockRequest(
            params: [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current_weather' => 'true',
            ],
            statusCode: 404
        );

        $this->expectExceptionObject(
            new BadRequestHttpException('Something went wrong.')
        );

        $this->openMeteoClient->getCurrent($latitude, $longitude);
    }

    public function testGetCurrentThrowsExceptionOnHttpError500(): void
    {
        $latitude = 48.8566;
        $longitude = 2.3522;
        $this->mockRequest(
            params: [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current_weather' => 'true',
            ],
            statusCode: 500
        );

        $this->expectExceptionObject(
            new BadRequestHttpException('Something went wrong.')
        );

        $this->openMeteoClient->getCurrent($latitude, $longitude);
    }

    public function testGetCurrentThrowsExceptionOnHttpError429(): void
    {
        $latitude = 51.5074;
        $longitude = -0.1278;
        $this->mockRequest(
            params: [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current_weather' => 'true',
            ],
            statusCode: 429
        );

        $this->expectExceptionObject(
            new BadRequestHttpException('Something went wrong.')
        );

        $this->openMeteoClient->getCurrent($latitude, $longitude);
    }

    public function testGetCurrentThrowsTimeoutException(): void
    {
        $this->client->expects($this->once())
            ->method('request')
            ->willThrowException(new class extends RuntimeException implements TimeoutExceptionInterface {});

        $result = $this->openMeteoClient->getCurrent(123.0, 456.0);

        self::assertSame(0.0, $result->temperature);
        self::assertNull($result->code);
        self::assertNull($result->wind);
    }

    /**
     * @param array<string, mixed> $params
     * @param null|array<string, mixed> $responseData
     */
    private function mockRequest(array $params, int $statusCode = 200, ?array $responseData = null): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn($statusCode);

        if (200 === $statusCode && null !== $responseData) {
            $response->expects($this->once())->method('toArray')->willReturn($responseData);
        }

        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'https://api.open-meteo.com/v1/forecast?' . http_build_query($params), [
                'timeout' => 5,
                'max_connect_duration' => 1,
            ])
            ->willReturn($response);
    }
}
