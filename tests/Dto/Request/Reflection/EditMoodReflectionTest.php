<?php

declare(strict_types=1);

namespace Continuum\Tests\Dto\Request\Reflection;

use Continuum\Dto\Request\Reflection\EditMoodReflection;
use Continuum\Enum\MoodType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EditMoodReflection::class)]
final class EditMoodReflectionTest extends TestCase
{
    public function testConstructor(): void
    {
        $dto = new EditMoodReflection(
            type: MoodType::Okay,
            text: 'hello',
        );

        self::assertSame(MoodType::Okay, $dto->type);
        self::assertSame('hello', $dto->text);
    }
}
