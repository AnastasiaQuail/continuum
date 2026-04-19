<?php

declare(strict_types=1);

namespace Continuum\Tests\Repository;

use Continuum\Entity\MoodReflection;
use Continuum\Repository\MoodReflectionRepository;
use Continuum\Tests\Test\AbstractRepositoryTestCase;
use DateTimeImmutable;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MoodReflectionRepository::class)]
final class MoodReflectionRepositoryTest extends AbstractRepositoryTestCase
{
    private MoodReflectionRepository $repository;

    #[Override]
    protected function setUp(): void
    {
        $this->repository = self::getContainer()->get(MoodReflectionRepository::class);
    }

    public function testFindPreviousDays(): void
    {
        $dates = [
            'one' => new DateTimeImmutable('-1 day'),
            'two' => new DateTimeImmutable('-5 days'),
            'three' => new DateTimeImmutable('-4 days'),
            'four' => new DateTimeImmutable('-10 days'),
            'five' => new DateTimeImmutable('-3 days'),
        ];

        foreach ($dates as $text => $date) {
            $mood = new MoodReflection(date: $date);
            $mood->text = $text;

            $this->repository->save($mood);
        }

        $moods = $this->repository->findPreviousDays(4);

        self::assertCount(2, $moods);
        self::assertSame('five', $moods[0]->text);
        self::assertSame('one', $moods[1]->text);
    }

    public function testFindByMonth(): void
    {
        $month = new DateTimeImmutable('2020-01-01');
        $dates = [
            'first' => new DateTimeImmutable('2020-01-02'),
            'last' => new DateTimeImmutable('2020-01-31'),
            'other' => new DateTimeImmutable('2020-02-01'),
        ];

        foreach ($dates as $text => $date) {
            $mood = new MoodReflection(date: $date);
            $mood->text = $text;

            $this->repository->save($mood);
        }

        $found = $this->repository->findByMonth($month);

        self::assertCount(2, $found);
        self::assertSame('first', $found[0]->text);
        self::assertSame('last', $found[1]->text);
    }

    public function testFindOneByDay(): void
    {
        $dateNow = new DateTimeImmutable();
        $dateAfterWeek = $dateNow->modify('+1 week');

        foreach (['now' => $dateNow, 'after_week' => $dateAfterWeek] as $text => $date) {
            $mood = new MoodReflection(date: $date);
            $mood->text = $text;

            $this->repository->save($mood);
        }

        $mood = $this->repository->findOneByDay($dateAfterWeek);

        self::assertNotNull($mood);
        self::assertSame('after_week', $mood->text);
    }

    public function testFindOneLast(): void
    {
        $dates = [
            'one' => new DateTimeImmutable('-5 day'),
            'two' => new DateTimeImmutable('-1 days'),
            'three' => new DateTimeImmutable('-4 days'),
            'four' => new DateTimeImmutable('-10 days'),
            'five' => new DateTimeImmutable('-3 days'),
        ];

        foreach ($dates as $text => $date) {
            $mood = new MoodReflection(date: $date);
            $mood->text = $text;

            $this->repository->save($mood);
        }

        $mood = $this->repository->findOneLast();

        self::assertNotNull($mood);
        self::assertSame('two', $mood->text);
    }

    public function testFindOneLastNotFound(): void
    {
        $mood = $this->repository->findOneLast();

        self::assertNull($mood);
    }
}
