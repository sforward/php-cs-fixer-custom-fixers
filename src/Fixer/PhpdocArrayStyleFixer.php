<?php declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer: custom fixers.
 *
 * (c) 2018 Kuba Werłos
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace PhpCsFixerCustomFixers\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;

final class PhpdocArrayStyleFixer extends AbstractTypesFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Generic array style should be used in PHPDoc.',
            [
                new CodeSample(
                    '<?php
/**
 * @return int[]
 */
 function foo() { return [1, 2]; }
',
                ),
            ],
            '',
        );
    }

    /**
     * Must run before PhpdocAlignFixer, PhpdocTypeListFixer, PhpdocTypesOrderFixer.
     */
    public function getPriority(): int
    {
        return 2;
    }

    protected function fixType(string $type): string
    {
        do {
            $type = Preg::replace('/([\\\\a-zA-Z0-9_>]+)\[\]/', 'array<$1>', $type, -1, $count);
        } while ($count > 0);

        return $type;
    }
}
