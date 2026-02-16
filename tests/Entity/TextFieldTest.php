<?php

declare(strict_types=1);

namespace Continuum\Tests\Entity;

use Continuum\Entity\TextField;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TextField::class)]
final class TextFieldTest extends TestCase
{
    public function testCreate(): void
    {
        $textField = new TextField('example text');

        self::assertSame('example text', $textField->text);
        self::assertFalse($textField->isPrivate);
    }

    public function testCreatePrivate(): void
    {
        $textField = new TextField(
            text: 'example text',
            isPrivate: true,
        );

        self::assertTrue($textField->isPrivate);
    }

    public function testCreateEmptyText(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException('Text cannot be empty.'));

        $textField = new TextField('');

        self::assertSame('--- this assert only for phpstorm ---', $textField->text);
    }
}
