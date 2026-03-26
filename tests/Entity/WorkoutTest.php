<?php

declare(strict_types=1);

namespace Continuum\Tests\Entity;

use Continuum\Entity\Workout;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\Uid\UuidV7;

#[CoversClass(Workout::class)]
final class WorkoutTest extends TestCase
{
    public function testCreate(): void
    {
        $workout = new Workout();
        $date = new DateTimeImmutable();
        new ReflectionProperty(Workout::class, 'date')->setValue($workout, $date);

        self::assertInstanceOf(UuidV7::class, $workout->id);
        self::assertSame($date->getTimestamp(), $workout->date->getTimestamp());
        self::assertTrue($workout->workoutExercises->isEmpty());
    }
}
