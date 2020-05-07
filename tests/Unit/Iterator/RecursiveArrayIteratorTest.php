<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Iterator;

use DigitalRevolution\SymfonyRequestValidation\Iterator\RecursiveArrayIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Iterator\RecursiveArrayIterator
 * @covers ::__construct
 */
class RecursiveArrayIteratorTest extends TestCase
{
    /**
     * @covers ::iterate
     * @covers ::walkArray
     */
    public function testIterateEmptyArray(): void
    {
        static::assertSame(
            [],
            (new RecursiveArrayIterator(
                [],
                static function () {
                }
            ))->iterate()
        );
    }

    /**
     * @covers ::iterate
     * @covers ::walkArray
     */
    public function testIterateSimpleArray(): void
    {
        $data   = ['a' => 'b'];
        $result =
            (new RecursiveArrayIterator(
                $data,
                static function ($key, $value): string {
                    static::assertSame('a', $key);
                    static::assertSame('b', $value);
                    return $value;
                }
            ))->iterate();
        static::assertSame($data, $result);
    }

    /**
     * @covers ::iterate
     * @covers ::walkArray
     */
    public function testIterateNestedArray(): void
    {
        $data   = ['a' => ['b' => ['c' => 'd']]];
        $result =
            (new RecursiveArrayIterator(
                $data,
                static function ($key, $value): string {
                    static::assertSame('a.b.c', $key);
                    static::assertSame('d', $value);
                    return $value;
                }
            ))->iterate();
        static::assertSame($data, $result);
    }

    /**
     * @covers ::iterate
     * @covers ::walkArray
     */
    public function testIterateModifySimpleArray(): void
    {
        $data   = ['a' => 'b'];
        $result =
            (new RecursiveArrayIterator(
                $data,
                static function ($key, $value): string {
                    static::assertSame('a', $key);
                    static::assertSame('b', $value);
                    return $value . 'b';
                }
            ))->iterate();
        static::assertNotSame($data, $result);
        static::assertSame(['a' => 'bb'], $result);
    }

    /**
     * @covers ::iterate
     * @covers ::walkArray
     */
    public function testIterateModifyNestedArray(): void
    {
        $result =
            (new RecursiveArrayIterator(
                [
                    'a1' => ['b' => ['c' => 'd', 'e' => 'f']],
                    'a2' => ['b' => ['c' => 'd', 'e' => 'f']],
                ],
                static function ($key, $value): string {
                    return $value . 'b';
                }
            ))->iterate();

        $expected = [
            'a1' => [
                'b' => [
                    'c' => 'db',
                    'e' => 'fb'
                ]
            ],
            'a2' => [
                'b' => [
                    'c' => 'db',
                    'e' => 'fb'
                ]
            ]
        ];
        static::assertSame($expected, $result);
    }
}
