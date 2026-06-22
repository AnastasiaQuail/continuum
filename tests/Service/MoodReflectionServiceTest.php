<?php

declare(strict_types=1);

namespace Continuum\Tests\Service;

use Continuum\Dto\Request\Reflection\EditMoodReflection;
use Continuum\Entity\MoodReflection;
use Continuum\Enum\MoodType;
use Continuum\Repository\MoodReflectionRepositoryInterface;
use Continuum\Service\MoodReflectionService;
use DateTimeImmutable;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(MoodReflectionService::class)]
final class MoodReflectionServiceTest extends TestCase
{
    private MockObject&MoodReflectionRepositoryInterface $repository;
    private MoodReflectionService $service;

    #[Override]
    protected function setUp(): void
    {
        $this->repository = $this->createMock(MoodReflectionRepositoryInterface::class);
        $this->service = new MoodReflectionService(trendDuration: 3, repository: $this->repository);
    }

    public function testGetPreviousDaysWithExplicitDays(): void
    {
        $days = 4;
        $firstMoodReflection = new MoodReflection(date: new DateTimeImmutable('-3 days'));
        $firstMoodReflection->text = 'old';

        $secondMoodReflection = new MoodReflection(date: new DateTimeImmutable('-1 days'));
        $secondMoodReflection->text = 'recent';
        $this->repository->expects($this->once())->method('findPreviousDays')->with($days)
            ->willReturn([$firstMoodReflection, $secondMoodReflection]);

        $found = $this->service->getPreviousDays($days);

        self::assertCount($days, $found);
        self::assertSame(
            [
                new DateTimeImmutable('-3 days')->format('Y-m-d') => 'old',
                new DateTimeImmutable('-2 days')->format('Y-m-d') => null,
                new DateTimeImmutable('-1 days')->format('Y-m-d') => 'recent',
                new DateTimeImmutable('0 days')->format('Y-m-d') => null,
            ],
            array_map(static fn (?MoodReflection $mood): ?string => $mood?->text, $found),
        );
    }

    public function testGetPreviousDaysUsesTrendDurationWhenNull(): void
    {
        // trendDuration set to 3 in setUp
        $this->repository->expects($this->once())
            ->method('findPreviousDays')
            ->with(3)
            ->willReturn([]);

        $found = $this->service->getPreviousDays();

        self::assertCount(3, $found);
        self::assertSame([], array_filter($found));
    }

    public function testGetByMonth(): void
    {
        $month = new DateTimeImmutable('2020-01-01');
        $firstMoodReflection = new MoodReflection(date: new DateTimeImmutable('2020-01-02'));
        $firstMoodReflection->text = 'first';

        $secondMoodReflection = new MoodReflection(date: new DateTimeImmutable('2020-01-31'));
        $secondMoodReflection->text = 'last';
        $this->repository->expects($this->once())->method('findByMonth')->with($month)
            ->willReturn([$firstMoodReflection, $secondMoodReflection]);

        $found = $this->service->getByMonth($month);

        self::assertCount(2, $found);
        self::assertSame(
            [
                '2020-01-02' => 'first',
                '2020-01-31' => 'last',
            ],
            array_map(static fn (MoodReflection $mood): string => $mood->text, $found),
        );
    }

    public function testFindMoodByDay(): void
    {
        $day = new DateTimeImmutable('2020-01-01');
        $moodReflection = new MoodReflection(date: $day);
        $moodReflection->text = 'x';

        $this->repository->expects($this->once())->method('findOneByDay')->with($day)->willReturn($moodReflection);

        $found = $this->service->findMoodByDay($day);

        self::assertNotNull($found);
        self::assertSame('x', $found->text);
    }

    public function testFindLastMood(): void
    {
        $moodReflection = new MoodReflection(date: new DateTimeImmutable('2020-01-01'));
        $moodReflection->text = 'x';

        $this->repository->expects($this->once())->method('findOneLast')->willReturn($moodReflection);

        $found = $this->service->findLastMood();

        self::assertNotNull($found);
        self::assertSame('x', $found->text);
    }

    public function testFindLastMoodWhenNull(): void
    {
        $this->repository->expects($this->once())->method('findOneLast')->willReturn(null);

        $found = $this->service->findLastMood();

        self::assertNull($found);
    }

    public function testSaveCreatesNewWhenNull(): void
    {
        $day = new DateTimeImmutable('2025-01-01');
        $dto = new EditMoodReflection(MoodType::Good, 'hello');
        $this->repository->expects($this->once())->method('save');

        $found = $this->service->save($day, $dto);

        self::assertSame($day->format('Y-m-d'), $found->date->format('Y-m-d'));
        self::assertSame(MoodType::Good, $found->type);
        self::assertSame('hello', $found->text);
    }

    public function testSaveUpdatesExisting(): void
    {
        $day = new DateTimeImmutable('2025-02-02');
        $existedMoodReflection = new MoodReflection(date: $day);
        $existedMoodReflection->text = 'old';
        $existedMoodReflection->type = MoodType::Okay;

        $dto = new EditMoodReflection(MoodType::Bad, 'new-text');
        $this->repository->expects($this->once())->method('save');

        $found = $this->service->save($day, $dto, $existedMoodReflection);

        self::assertSame($existedMoodReflection, $found);
        self::assertSame(MoodType::Bad, $found->type);
        self::assertSame('new-text', $found->text);
    }
}
