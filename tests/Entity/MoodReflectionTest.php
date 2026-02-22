<?php

declare(strict_types=1);

namespace Continuum\Tests\Entity;

use Continuum\Entity\MoodReflection;
use Continuum\Enum\MoodType;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV7;

#[CoversClass(MoodReflection::class)]
final class MoodReflectionTest extends TestCase
{
    public function testCreate(): void
    {
        $moodReflection = new MoodReflection(
            date: $date = new DateTimeImmutable('2020-01-01 00:00:00'),
        );

        self::assertInstanceOf(UuidV7::class, $moodReflection->id);
        self::assertSame($date->getTimestamp(), $moodReflection->date->getTimestamp());
        self::assertSame(MoodType::Okay, $moodReflection->type);
        self::assertSame('', $moodReflection->text);
    }

    public function testType(): void
    {
        $moodReflection = new MoodReflection(
            date: new DateTimeImmutable(),
        );
        $moodReflection->type = MoodType::Good;

        self::assertSame(MoodType::Good, $moodReflection->type);
    }

    public function testText(): void
    {
        $moodReflection = new MoodReflection(
            date: new DateTimeImmutable(),
        );
        $moodReflection->text = 'example text';

        self::assertSame('example text', $moodReflection->text);
    }
}
