<?php

declare(strict_types=1);

namespace Continuum\Tools\PhpStan\Rule;

use Override;
use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Concat;
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
    #[Override]
    public function getNodeType(): string
    {
        return Concat::class;
    }

    #[Override]
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->left instanceof Dir) {
            return [];
        }

        if (!$node->right instanceof String_) {
            return [
                RuleErrorBuilder::message('Something wrong. Right side of concatenation is not a string.')
                    ->identifier('path.notExists')
                    ->build(),
            ];
        }

        $path = dirname($scope->getFile()) . $node->right->value;

        if (
            (false === $realPath = realpath($path))
            || !file_exists($realPath)
        ) {
            if (str_contains($path, '/*')) {
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
}
