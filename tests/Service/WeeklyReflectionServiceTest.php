<?php

declare(strict_types=1);

namespace Continuum\Tests\Service;

use Continuum\Dto\Request\Reflection\EditWeeklyReflection;
use Continuum\Entity\TextField;
use Continuum\Entity\WeeklyReflection;
use Continuum\Repository\WeeklyReflectionRepositoryInterface;
use Continuum\Service\WeeklyReflectionService;
use DateTimeImmutable;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(WeeklyReflectionService::class)]
final class WeeklyReflectionServiceTest extends TestCase
{
    private MockObject&WeeklyReflectionRepositoryInterface $repository;
    private WeeklyReflectionService $service;

    #[Override]
    protected function setUp(): void
    {
        $this->repository = $this->createMock(WeeklyReflectionRepositoryInterface::class);
        $this->service = new WeeklyReflectionService($this->repository);
    }

    /**
     * @param list<WeeklyReflection> $weeklyReflections
     */
    #[DataProvider('provideGetByMonthCases')]
    public function testGetByMonth(DateTimeImmutable $month, array $weeklyReflections): void
    {
        $dates = [];
        $sunday = $month->modify('sunday');
        $endDate = $month->modify('+1 month');

        do {
            $dates[] = $sunday;
            $sunday = $sunday->modify('+1 week');
        } while ($sunday < $endDate);

        $this->repository->expects($this->once())->method('findByDays')
            ->with(...$dates)->willReturn($weeklyReflections);

        $foundWeeklyReflections = $this->service->getByMonth($month);

        self::assertCount(count($dates), $foundWeeklyReflections);
        self::assertSame(
            array_map(
                static fn (DateTimeImmutable $date): string => $date->format('Y-m-d'),
                $dates,
            ),
            array_keys($foundWeeklyReflections),
        );
        self::assertSame(
            array_map(
                static fn (WeeklyReflection $wr): string => $wr->date->format('Y-m-d'),
                $weeklyReflections,
            ),
            array_map(
                static fn (WeeklyReflection $fwr): string => $fwr->date->format('Y-m-d'),
                array_values(array_filter($foundWeeklyReflections)),
            )
        );
    }

    /**
     * @return iterable<array{0: DateTimeImmutable, 1: list<WeeklyReflection>}>
     */
    public static function provideGetByMonthCases(): iterable
    {
        yield [
            $month = new DateTimeImmutable('2025-11-01'),
            [
                new WeeklyReflection(
                    $month->modify('+8 days')->modify('sunday'),
                    new TextField('-'),
                    new TextField('-'),
                    new TextField('-'),
                ),
                new WeeklyReflection(
                    $month->modify('+20 days')->modify('sunday'),
                    new TextField('-'),
                    new TextField('-'),
                    new TextField('-'),
                ),
            ],
        ];

        yield [
            new DateTimeImmutable('2026-01-01'),
            [],
        ];

        yield [
            $month = new DateTimeImmutable('2025-12-01'),
            [
                new WeeklyReflection(
                    $month->modify('+15 days')->modify('sunday'),
                    new TextField('-'),
                    new TextField('-'),
                    new TextField('-'),
                ),
            ],
        ];
    }

    public function testFindByWeek(): void
    {
        $weeklyReflection = new WeeklyReflection(
            new DateTimeImmutable('sunday'),
            new TextField('-'),
            new TextField('-'),
            new TextField('-'),
        );
        $this->repository->expects($this->once())->method('findOneByDay')
            ->with($weeklyReflection->date)->willReturn($weeklyReflection);

        $foundWeeklyReflection = $this->service->findByWeek($weeklyReflection->date);

        self::assertNotNull($foundWeeklyReflection);
        self::assertSame($weeklyReflection->date->format('Y-m-d'), $foundWeeklyReflection->date->format('Y-m-d'));
    }

    #[DataProvider('provideSaveCases')]
    public function testSave(?DateTimeImmutable $week, EditWeeklyReflection $dto, WeeklyReflection $result): void
    {
        $weeklyReflection = null;
        if (null === $week) {
            $week = new DateTimeImmutable();
            $weeklyReflection = new WeeklyReflection(
                date: new DateTimeImmutable('2025-01-01'),
                joy: new TextField('-'),
                difficulty: new TextField('-'),
                achievement: new TextField('-')
            );
        }

        $this->repository->expects($this->once())->method('save');

        $found = $this->service->save($week, $weeklyReflection, $dto);

        self::assertSame($result->date->format('Y-m-d'), $found->date->format('Y-m-d'));
        self::assertSame($result->joy->text, $found->joy->text);
        self::assertSame($result->joy->isPrivate, $found->joy->isPrivate);
        self::assertSame($result->difficulty->text, $found->difficulty->text);
        self::assertSame($result->difficulty->isPrivate, $found->difficulty->isPrivate);
        self::assertSame($result->achievement->text, $found->achievement->text);
        self::assertSame($result->achievement->isPrivate, $found->achievement->isPrivate);
    }

    /**
     * @return iterable<array{0: null|DateTimeImmutable, 1: EditWeeklyReflection, 2: WeeklyReflection}>
     */
    public static function provideSaveCases(): iterable
    {
        yield [
            $week = new DateTimeImmutable('2020-01-01')->modify('sunday'),
            new EditWeeklyReflection(
                joy: 'joy_text',
                isJoyPrivate: false,
                difficulty: 'difficulty_text',
                isDifficultyPrivate: true,
                achievement: '---',
                isAchievementPrivate: false,
            ),
            new WeeklyReflection(
                date: $week,
                joy: new TextField('joy_text'),
                difficulty: new TextField('difficulty_text', true),
                achievement: new TextField('---'),
            ),
        ];

        yield [
            null,
            new EditWeeklyReflection(
                joy: '---',
                isJoyPrivate: true,
                difficulty: 'difficulty_text',
                isDifficultyPrivate: false,
                achievement: 'achievement_text',
                isAchievementPrivate: true,
            ),
            new WeeklyReflection(
                date: new DateTimeImmutable('2025-01-01'),
                joy: new TextField('---', true),
                difficulty: new TextField('difficulty_text'),
                achievement: new TextField('achievement_text', true),
            ),
        ];

        yield [
            null,
            new EditWeeklyReflection(
                joy: 'joy_text',
                isJoyPrivate: false,
                difficulty: 'other_text',
                isDifficultyPrivate: false,
                achievement: '000',
                isAchievementPrivate: true,
            ),
            new WeeklyReflection(
                date: new DateTimeImmutable('2025-01-01'),
                joy: new TextField('joy_text'),
                difficulty: new TextField('other_text'),
                achievement: new TextField('000', true),
            ),
        ];
    }
}
