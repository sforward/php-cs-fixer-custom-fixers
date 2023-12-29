<?php declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer: custom fixers.
 *
 * (c) 2018 Kuba Werłos
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace PhpCsFixerCustomFixers\Analyzer\Analysis;

/**
 * @internal
 */
final class DataProviderAnalysis
{
    /** @var string */
    private $name;

    /** @var int */
    private $nameIndex;

    /** @var list<int> */
    private $usageIndices;

    /**
     * @param list<int> $usageIndices
     */
    public function __construct(string $name, int $nameIndex, array $usageIndices)
    {
        $this->name = $name;
        $this->nameIndex = $nameIndex;
        $this->usageIndices = $usageIndices;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNameIndex(): int
    {
        return $this->nameIndex;
    }

    /**
     * @return list<int>
     */
    public function getUsageIndices(): array
    {
        return $this->usageIndices;
    }
}
