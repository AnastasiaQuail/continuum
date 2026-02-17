<?php

declare(strict_types=1);

namespace Continuum\Tools\PhpStan\Rule;

use Override;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Concat>
 */
final readonly class FileAndDirectoryExistingRule implements Rule
{
    private const array SKIP_PATHS = [
        '/assets/vendor',
        '/var/cache/prod/Continuum_KernelProdContainer.preload.php',
        '/var/cache/rector',
    ];

    #[Override]
    public function getNodeType(): string
    {
        return Concat::class;
    }

    #[Override]
    public function processNode(Node $node, Scope $scope): array
    {
        if (
            !$node->left instanceof Dir
            && !(
                $node->left instanceof FuncCall
                // @phpstan-ignore property.notFound
                && 'dirname' === $node->left->name->name
            )
        ) {
            return [];
        }

        if (!$node->right instanceof String_) {
            return [
                RuleErrorBuilder::message('Something wrong. Right side of concatenation is not a string.')
                    ->identifier('path.notExists')
                    ->build(),
            ];
        }

        $path = $this->getDirectory($node->left, $scope->getFile()) . $node->right->value;

        if (
            (false === $realPath = realpath($path))
            || !file_exists($realPath)
        ) {
            if (str_contains($path, '/*')) {
                return [];
            }

            if (array_any(self::SKIP_PATHS, static fn (string $skipPath): bool => str_ends_with($path, $skipPath))) {
                return [];
            }

            $notExistsPath = false !== $realPath ? $realPath : $path;

            return [
                RuleErrorBuilder::message(sprintf('File or directory "%s" does not exist.', $notExistsPath))
                    ->identifier('path.notExists')
                    ->build(),
            ];
        }

        return [];
    }

    private function getDirectory(Dir|FuncCall $node, string $path): string
    {
        if ($node instanceof Dir) {
            return dirname($path);
        }

        $leftNode = array_first($node->args);
        $rightNode = array_last($node->args);

        if ($leftNode instanceof Arg) {
            $leftNode = $leftNode->value;

            if (!$leftNode instanceof Dir) {
                return '';
            }
        }

        $levels = 1;

        if ($rightNode instanceof Arg) {
            $rightNode = $rightNode->value;

            if ($rightNode instanceof Int_) {
                /** @var non-negative-int $levels */
                $levels = $rightNode->value;
            }
        }

        return dirname($path, $levels + 1);
    }
}
