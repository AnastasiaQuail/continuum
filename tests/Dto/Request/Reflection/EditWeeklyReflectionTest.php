<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Request\Reflection;

use Continuum\Dto\Request\Reflection\EditWeeklyReflection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EditWeeklyReflection::class)]
final class EditWeeklyReflectionTest extends TestCase
{
    public function testConstructor(): void
    {
        $dto = new EditWeeklyReflection(
            joy: 'joy',
            isJoyPrivate: true,
            difficulty: 'diff',
            isDifficultyPrivate: false,
            achievement: 'ach',
            isAchievementPrivate: true,
        );

        self::assertSame('joy', $dto->joy);
        self::assertTrue($dto->isJoyPrivate);
        self::assertSame('diff', $dto->difficulty);
        self::assertFalse($dto->isDifficultyPrivate);
        self::assertSame('ach', $dto->achievement);
        self::assertTrue($dto->isAchievementPrivate);
    }
}
