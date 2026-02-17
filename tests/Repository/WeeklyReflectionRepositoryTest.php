<?php

declare(strict_types=1);

namespace Continuum\Tests\Repository;

use Continuum\Entity\TextField;
use Continuum\Entity\WeeklyReflection;
use Continuum\Repository\WeeklyReflectionRepository;
use Continuum\Tests\Test\AbstractRepositoryTestCase;
use DateTimeImmutable;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(WeeklyReflectionRepository::class)]
final class WeeklyReflectionRepositoryTest extends AbstractRepositoryTestCase
{
    private WeeklyReflectionRepository $repository;

    #[Override]
    protected function setUp(): void
    {
        $this->repository = self::getContainer()->get(WeeklyReflectionRepository::class);
    }

    public function testFindByDays(): void
    {
        $dates = [
            'one' => new DateTimeImmutable('+1 week'),
            'two' => new DateTimeImmutable('+1 year'),
            'three' => new DateTimeImmutable('+1 month'),
            'four' => new DateTimeImmutable('+1 day'),
        ];

        foreach ($dates as $joyText => $date) {
            $this->repository->save(
                new WeeklyReflection(
                    date: $date,
                    joy: new TextField($joyText),
                    difficulty: new TextField('-'),
                    achievement: new TextField('-'),
                )
            );
        }

        $weeklyReflections = $this->repository->findByDays(...$dates);

        self::assertCount(4, $weeklyReflections);
        self::assertSame('four', $weeklyReflections[0]->joy->text);
        self::assertSame('one', $weeklyReflections[1]->joy->text);
        self::assertSame('three', $weeklyReflections[2]->joy->text);
        self::assertSame('two', $weeklyReflections[3]->joy->text);
    }

    public function testFindByDaysEmpty(): void
    {
        $date = new DateTimeImmutable();
        $this->repository->save(
            new WeeklyReflection(
                date: $date,
                joy: new TextField('-'),
                difficulty: new TextField('-'),
                achievement: new TextField('-'),
            )
        );

        $weeklyReflections = $this->repository->findByDays($date->modify('+1 day'));

        self::assertCount(0, $weeklyReflections);
    }

    public function testFindOneByDay(): void
    {
        $dateNow = new DateTimeImmutable();
        $dateAfterWeek = $dateNow->modify('+1 week');

        foreach (['now' => $dateNow, 'after_week' => $dateAfterWeek] as $joyText => $date) {
            $this->repository->save(
                new WeeklyReflection(
                    date: $date,
                    joy: new TextField($joyText),
                    difficulty: new TextField('-'),
                    achievement: new TextField('-'),
                )
            );
        }

        $weeklyReflection = $this->repository->findOneByDay($dateAfterWeek);

        self::assertNotNull($weeklyReflection);
        self::assertSame('after_week', $weeklyReflection->joy->text);
    }
}
