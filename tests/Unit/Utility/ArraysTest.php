<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Utility;

use DigitalRevolution\SymfonyRequestValidation\Utility\ArrayAssignException;
use DigitalRevolution\SymfonyRequestValidation\Utility\Arrays;
use DigitalRevolution\SymfonyRequestValidation\Utility\PathFactory;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Utility\Arrays
 */
class ArraysTest extends TestCase
{
    /**
     *
     * @throws ArrayAssignException
     */
    public function testAssignToPath(): void
    {
        $data = ['a' => []];

        static::assertSame(['a' => ['c']], Arrays::assignToPath($data, ['a', '0'], 'c'));
    }

    /**
     * @covers ::findData
     */
    public function testFindData(): void
    {
        static::assertSame(['a' => 'b'], Arrays::findData(PathFactory::createFromString('a'), ['a' => 'b']));
        static::assertSame(['a.b' => 'c'], Arrays::findData(PathFactory::createFromString('a.b'), ['a' => ['b' => 'c']]));
        static::assertSame(['a.0' => 'b'], Arrays::findData(PathFactory::createFromString('a.#0'), ['a' => ['b']]));
        static::assertSame(['a.0.b' => 'c'], Arrays::findData(PathFactory::createFromString('a.#0.b'), ['a' => [['b' => 'c']]]));

        // key - wildcard
        static::assertSame(
            ['a.0' => 'b', 'a.1' => 'd'],
            Arrays::findData(PathFactory::createFromString('a.*'), ['a' => ['b', 'd']])
        );

        // key - wildcard - key
        static::assertSame(
            ['a.0.b' => 'c', 'a.1.b' => 'd'],
            Arrays::findData(PathFactory::createFromString('a.*.b'), ['a' => [['b' => 'c'], ['b' => 'd']]])
        );

        // key - wildcard - wildcard
        static::assertSame(
            ['a.0.b' => 'c', 'a.1.b' => 'd'],
            Arrays::findData(PathFactory::createFromString('a.*.*'), ['a' => [['b' => 'c'], ['b' => 'd']]])
        );
    }
}
