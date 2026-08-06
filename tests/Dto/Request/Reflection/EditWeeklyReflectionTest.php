<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Request\Reflection;

use Continuum\Dto\Request\Reflection\EditWeeklyReflection;
use Continuum\Dto\Request\TextField;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EditWeeklyReflection::class)]
final class EditWeeklyReflectionTest extends TestCase
{
    public function testConstructor(): void
    {
        $joy = new TextField(text: 'joy', isPrivate: true);
        $difficulty = new TextField(text: 'diff', isPrivate: false);
        $achievement = new TextField(text: 'ach', isPrivate: true);

        $dto = new EditWeeklyReflection(
            joy: $joy,
            difficulty: $difficulty,
            achievement: $achievement,
        );

        self::assertSame($joy, $dto->joy);
        self::assertSame($difficulty, $dto->difficulty);
        self::assertSame($achievement, $dto->achievement);
    }
}
