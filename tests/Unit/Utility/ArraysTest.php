<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Unit\Utility;

use DigitalRevolution\SymfonyValidationShorthand\Utility\Arrays;
use DigitalRevolution\SymfonyValidationShorthand\Utility\InvalidArrayPathException;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyValidationShorthand\Utility\Arrays
 */
class ArraysTest extends TestCase
{
    /**
     * @param array<int|string, mixed> $data
     * @param array<int|string, mixed> $expected
     * @dataProvider dataProvider
     * @covers ::assignToPath
     * @throws InvalidArrayPathException
     */
    public function testAssignToPath(array $data, string $path, string $value, array $expected): void
    {
        static::assertSame($expected, Arrays::assignToPath($data, explode('.', $path), $value));
    }

    /**
     * @return Generator<string, array<array<int|string, mixed>, string, string, array<int|string, mixed>>>
     */
    public function dataProvider(): Generator
    {
        yield 'assign empty array: key => value' => [[], 'a', 'b', ['a' => 'b']];
        yield 'assign empty array: key, key + value' => [[], 'a.b', 'c', ['a' => ['b' => 'c']]];
        yield 'assign non-empty array: key + value' => [['b' => 'a'], 'a', 'b', ['b' => 'a', 'a' => 'b']];
        yield 'assign non-empty nested array: key + value' => [['a' => ['b' => 'c']], 'a.c', 'd', ['a' => ['b' => 'c', 'c' => 'd']]];
        yield 'assign numeric key array: key, index + value' => [[], 'a.0', 'b', ['a' => ['b']]];
    }

    /**
     * Assign a value with an empty path is not possible.
     *
     * @covers ::assignToPath
     * @throws InvalidArrayPathException
     */
    public function testAssignToPathEmptyPathIsInvalidArgumentException(): void
    {
        $data = [];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("\$path can't be empty");
        Arrays::assignToPath($data, [], 'd');
    }

    /**
     * Assigning a new value to an already assigned value will result in an exception.
     *
     * @covers ::assignToPath
     * @throws InvalidArrayPathException
     */
    public function testAssignToPathAlreadyAssignKeyException(): void
    {
        $data = ['a' => ['b' => 'c']];

        $this->expectException(InvalidArrayPathException::class);
        $this->expectExceptionMessage('is already assigned');
        Arrays::assignToPath($data, ['a', 'b'], 'd');
    }

    /**
     * Assigning a value to a path where somewhere in the path the value is not an array anymore, will result in exception.
     *
     * @covers ::assignToPath
     * @throws InvalidArrayPathException
     */
    public function testAssignToPathNotArrayValueException(): void
    {
        $data = ['unit' => 'test'];

        $this->expectException(InvalidArrayPathException::class);
        $this->expectExceptionMessage('is not an array');
        Arrays::assignToPath($data, ['unit', 'test'], 'case');
    }
}
