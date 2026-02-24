<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Response;

use Continuum\Component\Weather\Dto\Weather;
use Continuum\Component\Weather\Dto\Wind;
use Continuum\Component\Weather\WmoCode;
use Continuum\Dto\Response\CoupleInformation;
use Continuum\Dto\Response\CoupleTogetherInformation;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CoupleInformation::class)]
final class CoupleInformationTest extends TestCase
{
    public function testConstructor(): void
    {
        $weather = new Weather(
            temperature: 10.0,
            code: WmoCode::Snow,
            wind: new Wind(speed: 1.0, direction: 2.0),
        );
        $time = new DateTimeImmutable('-2 days');
        $together = new CoupleTogetherInformation(together: new DateInterval('P7D'));

        $dto = new CoupleInformation(
            weather: $weather,
            time: $time,
            partnerWeather: $weather,
            partnerTime: $time,
            together: $together,
            distance: 5.5,
        );

        self::assertSame($weather, $dto->weather);
        self::assertSame($time, $dto->time);
        self::assertSame($weather, $dto->partnerWeather);
        self::assertSame($time, $dto->partnerTime);
        self::assertSame($together, $dto->together);
        self::assertSame(5.5, $dto->distance);
    }
}
