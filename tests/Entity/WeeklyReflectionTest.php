<?php

declare(strict_types=1);

namespace Continuum\Tests\Entity;

use Continuum\Entity\TextField;
use Continuum\Entity\WeeklyReflection;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV7;

#[CoversClass(WeeklyReflection::class)]
final class WeeklyReflectionTest extends TestCase
{
    public function testCreate(): void
    {
        $weeklyReflection = new WeeklyReflection(
            date: $date = new DateTimeImmutable('2020-01-01 00:00:00'),
            joy: new TextField('joy text', true),
            difficulty: new TextField('difficulty text', false),
            achievement: new TextField('achievement text', true),
        );

        self::assertInstanceOf(UuidV7::class, $weeklyReflection->id);
        self::assertSame($date->getTimestamp(), $weeklyReflection->date->getTimestamp());
        self::assertSame('joy text', $weeklyReflection->joy->text);
        self::assertTrue($weeklyReflection->joy->isPrivate);
        self::assertSame('difficulty text', $weeklyReflection->difficulty->text);
        self::assertFalse($weeklyReflection->difficulty->isPrivate);
        self::assertSame('achievement text', $weeklyReflection->achievement->text);
        self::assertTrue($weeklyReflection->achievement->isPrivate);
    }
}
