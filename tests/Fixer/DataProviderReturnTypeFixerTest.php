<?php declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer: custom fixers.
 *
 * (c) 2018 Kuba Werłos
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Fixer;

/**
 * @internal
 *
 * @covers \PhpCsFixerCustomFixers\Fixer\DataProviderReturnTypeFixer
 */
final class DataProviderReturnTypeFixerTest extends AbstractFixerTestCase
{
    private const TEMPLATE = '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     */
    public function testFoo() {}
    /**
     * @dataProvider provider
     */
    public function testBar() {}
    public function provideFooCases()%s {}
    public function provider()%s {}
    public function notProvider(): array {}
}';

    public function testIsRisky(): void
    {
        self::assertRiskiness(true);
    }

    public function testSuccessorName(): void
    {
        self::assertSuccessorName('php_unit_data_provider_return_type');
    }

    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'data provider with iterable return type' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     */
    public function testFoo() {}
    public function provideFooCases() : iterable {}
}',
        ];

        yield 'data provider without return type' => self::mapToTemplate(
            ': iterable',
            '',
        );

        yield 'data provider with array return type' => self::mapToTemplate(
            ': iterable',
            ': array',
        );

        yield 'data provider with return type and comment' => self::mapToTemplate(
            ': /* foo */ iterable',
            ': /* foo */ array',
        );

        yield 'data provider with return type namespaced class' => self::mapToTemplate(
            ': iterable',
            ': Foo\Bar',
        );

        yield 'data provider with iterable return type in different case' => self::mapToTemplate(
            ': iterable',
            ': Iterable',
        );

        yield 'multiple data providers' => [
            '<?php class FooTest extends TestCase {
                /**
                 * @dataProvider provider4
                 * @dataProvider provider1
                 * @dataProvider provider5
                 * @dataProvider provider6
                 * @dataProvider provider2
                 * @dataProvider provider3
                 */
                public function testFoo() {}
                public function provider1(): iterable {}
                public function provider2(): iterable {}
                public function provider3(): iterable {}
                public function provider4(): iterable {}
                public function provider5(): iterable {}
                public function provider6(): iterable {}
            }',
            '<?php class FooTest extends TestCase {
                /**
                 * @dataProvider provider4
                 * @dataProvider provider1
                 * @dataProvider provider5
                 * @dataProvider provider6
                 * @dataProvider provider2
                 * @dataProvider provider3
                 */
                public function testFoo() {}
                public function provider1() {}
                public function provider2() {}
                public function provider3() {}
                public function provider4() {}
                public function provider5() {}
                public function provider6() {}
            }',
        ];

        yield 'advanced case' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     * @dataProvider provideFooCases2
     */
    public function testFoo()
    {
        /**
         * @dataProvider someFunction
         */
        $foo = /** foo */ function ($x) { return $x + 1; };
        /**
         * @dataProvider someFunction2
         */
        /* foo */someFunction2();
    }
    /**
     * @dataProvider provideFooCases3
     */
    public function testBar() {}

    public function provideFooCases(): iterable {}
    public function provideFooCases2(): iterable {}
    public function provideFooCases3(): iterable {}
    public function someFunction() {}
    public function someFunction2() {}
}',
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     * @dataProvider provideFooCases2
     */
    public function testFoo()
    {
        /**
         * @dataProvider someFunction
         */
        $foo = /** foo */ function ($x) { return $x + 1; };
        /**
         * @dataProvider someFunction2
         */
        /* foo */someFunction2();
    }
    /**
     * @dataProvider provideFooCases3
     */
    public function testBar() {}

    public function provideFooCases() {}
    public function provideFooCases2() {}
    public function provideFooCases3() {}
    public function someFunction() {}
    public function someFunction2() {}
}',
        ];

        foreach (['abstract', 'final', 'private', 'protected', 'static', '/* private */'] as $modifier) {
            yield \sprintf('test function with %s modifier', $modifier) => [
                \sprintf('<?php
                    abstract class FooTest extends TestCase {
                        /**
                         * @dataProvider provideFooCases
                         */
                        %s function testFoo() %s
                        public function provideFooCases(): iterable {}
                    }
                ', $modifier, $modifier === 'abstract' ? ';' : '{}'),
                \sprintf('<?php
                    abstract class FooTest extends TestCase {
                        /**
                         * @dataProvider provideFooCases
                         */
                        %s function testFoo() %s
                        public function provideFooCases() {}
                    }
                ', $modifier, $modifier === 'abstract' ? ';' : '{}'),
            ];
        }
    }

    /**
     * @return array{string, string}
     */
    private static function mapToTemplate(string $expected, string $actual): array
    {
        return [
            \sprintf(self::TEMPLATE, $expected, $expected),
            \sprintf(self::TEMPLATE, $actual, $actual),
        ];
    }
}
