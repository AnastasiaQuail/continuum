<?php

declare(strict_types=1);

namespace Continuum\Tests\Service;

use Continuum\Dto\Request\Reflection\EditWeeklyReflection;
use Continuum\Dto\Request\TextField as TextFieldDto;
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
     * @param list<DateTimeImmutable> $expectDates
     * @param list<WeeklyReflection> $expectWeeklyReflections
     */
    #[DataProvider('provideGetByMonthCases')]
    public function testGetByMonth(
        DateTimeImmutable $month,
        ?DateTimeImmutable $currentDate,
        array $expectDates,
        array $expectWeeklyReflections
    ): void {
        $this->repository->expects($this->once())->method('findByDays')
            ->with(...$expectDates)->willReturn($expectWeeklyReflections);

        $foundWeeklyReflections = $this->service->getByMonth($month, $currentDate);

        self::assertCount(count($expectDates), $foundWeeklyReflections);
        self::assertSame(
            array_map(
                static fn (DateTimeImmutable $date): string => $date->format('Y-m-d'),
                $expectDates,
            ),
            array_keys($foundWeeklyReflections),
        );
        self::assertSame(
            array_map(
                static fn (WeeklyReflection $wr): string => $wr->date->format('Y-m-d'),
                $expectWeeklyReflections,
            ),
            array_map(
                static fn (WeeklyReflection $fwr): string => $fwr->date->format('Y-m-d'),
                array_values(array_filter($foundWeeklyReflections)),
            )
        );
    }

    /**
     * @return iterable<array{
     *     0: DateTimeImmutable,
     *     1: null|DateTimeImmutable,
     *     2: list<DateTimeImmutable>,
     *     3: list<WeeklyReflection>
     * }>
     */
    public static function provideGetByMonthCases(): iterable
    {
        yield [
            // saturday
            new DateTimeImmutable('2025-11-01'),
            null,
            [
                new DateTimeImmutable('2025-11-02'),
                new DateTimeImmutable('2025-11-09'),
                new DateTimeImmutable('2025-11-16'),
                new DateTimeImmutable('2025-11-23'),
                new DateTimeImmutable('2025-11-30'),
            ],
            [
                new WeeklyReflection(
                    new DateTimeImmutable('2025-11-09'),
                    new TextField('-'),
                    new TextField('-'),
                    new TextField('-'),
                ),
                new WeeklyReflection(
                    new DateTimeImmutable('2025-11-23'),
                    new TextField('-'),
                    new TextField('-'),
                    new TextField('-'),
                ),
            ],
        ];

        yield [
            // monday
            new DateTimeImmutable('2025-12-01'),
            null,
            [
                new DateTimeImmutable('2025-12-07'),
                new DateTimeImmutable('2025-12-14'),
                new DateTimeImmutable('2025-12-21'),
                new DateTimeImmutable('2025-12-28'),
            ],
            [
                new WeeklyReflection(
                    new DateTimeImmutable('2025-12-21'),
                    new TextField('-'),
                    new TextField('-'),
                    new TextField('-'),
                ),
            ],
        ];

        yield [
            // monday
            new DateTimeImmutable('2025-12-01'),
            // tuesday
            new DateTimeImmutable('2025-12-16'),
            [
                new DateTimeImmutable('2025-12-07'),
                new DateTimeImmutable('2025-12-14'),
                new DateTimeImmutable('2025-12-21'),
            ],
            [
                new WeeklyReflection(
                    new DateTimeImmutable('2025-12-14'),
                    new TextField('-'),
                    new TextField('-'),
                    new TextField('-'),
                ),
            ],
        ];

        yield [
            // thursday
            new DateTimeImmutable('2026-01-01'),
            null,
            [
                new DateTimeImmutable('2026-01-04'),
                new DateTimeImmutable('2026-01-11'),
                new DateTimeImmutable('2026-01-18'),
                new DateTimeImmutable('2026-01-25'),
            ],
            [],
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
                joy: new TextFieldDto(text: 'joy_text', isPrivate: false),
                difficulty: new TextFieldDto(text: 'difficulty_text', isPrivate: true),
                achievement: new TextFieldDto(text: '---', isPrivate: false),
            ),
            new WeeklyReflection(
                date: $week,
                joy: new TextField('joy_text'),
                difficulty: new TextField('difficulty_text', isPrivate: true),
                achievement: new TextField('---'),
            ),
        ];

        yield [
            null,
            new EditWeeklyReflection(
                joy: new TextFieldDto(text: '---', isPrivate: true),
                difficulty: new TextFieldDto(text: 'difficulty_text', isPrivate: false),
                achievement: new TextFieldDto(text: 'achievement_text', isPrivate: true),
            ),
            new WeeklyReflection(
                date: new DateTimeImmutable('2025-01-01'),
                joy: new TextField('---', isPrivate: true),
                difficulty: new TextField('difficulty_text'),
                achievement: new TextField('achievement_text', isPrivate: true),
            ),
        ];

        yield [
            null,
            new EditWeeklyReflection(
                joy: new TextFieldDto(text: 'joy_text', isPrivate: false),
                difficulty: new TextFieldDto(text: 'other_text', isPrivate: false),
                achievement: new TextFieldDto(text: '000', isPrivate: true),
            ),
            new WeeklyReflection(
                date: new DateTimeImmutable('2025-01-01'),
                joy: new TextField('joy_text'),
                difficulty: new TextField('other_text'),
                achievement: new TextField('000', isPrivate: true),
            ),
        ];
    }
}
