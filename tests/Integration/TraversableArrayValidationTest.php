<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Integration;

use ArrayIterator;
use Exception;

/**
 * @coversNothing
 */
class TraversableArrayValidationTest extends IntegrationTest
{
    /**
     * @throws Exception
     */
    public function testSingleDimensionArrayValidation(): void
    {
        $rules = ['*' => 'int'];

        $iterator = new ArrayIterator([1, 2]);
        static::assertHasNoViolations($iterator, $rules);

        $iterator = new ArrayIterator([]);
        static::assertHasNoViolations($iterator, $rules);

        $iterator = new ArrayIterator([1, 'a']);
        static::assertHasViolations($iterator, $rules);
    }

    /**
     * @throws Exception
     */
    public function testSingleDimensionArrayWithRequiredElement(): void
    {
        $rules = ['*' => 'required|int'];

        $iterator = new ArrayIterator([]);
        static::assertHasViolations($iterator, $rules);

        $iterator = new ArrayIterator([3]);
        static::assertHasNoViolations($iterator, $rules);

        $iterator = new ArrayIterator(['a']);
        static::assertHasViolations($iterator, $rules);

        $iterator = new ArrayIterator([null]);
        static::assertHasViolations($iterator, $rules);
    }

    /**
     * @throws Exception
     */
    public function testSingleDimensionArrayWithNullableElement(): void
    {
        $rules = ['*' => 'int|nullable'];

        $iterator = new ArrayIterator([]);
        static::assertHasNoViolations($iterator, $rules);

        $iterator = new ArrayIterator([null]);
        static::assertHasNoViolations($iterator, $rules);

        $iterator = new ArrayIterator([1]);
        static::assertHasNoViolations($iterator, $rules);

        $iterator = new ArrayIterator([1, null]);
        static::assertHasNoViolations($iterator, $rules);
    }

    /**
     * @throws Exception
     */
    public function testTraversableArrayWithColumnData(): void
    {
        $rules = [
            '*.0' => 'required|int',
            '*.1' => 'string'
        ];

        // empty array
        $iterator = new ArrayIterator([]);
        static::assertHasNoViolations($iterator, $rules);

        // one entry: integer
        $iterator = new ArrayIterator([[5]]);
        static::assertHasNoViolations($iterator, $rules);

        // one entry: integer + string
        $iterator = new ArrayIterator([[5, 'test']]);
        static::assertHasNoViolations($iterator, $rules);

        // one entry: missing integer
        $iterator = new ArrayIterator([[1 => 'test']]);
        static::assertHasViolations($iterator, $rules);

        // double entries
        $iterator = new ArrayIterator([[1, 'test'], [2, 'unit']]);
        static::assertHasNoViolations($iterator, $rules);

        // invalid double entries
        $iterator = new ArrayIterator([[1, 2], [3, 4]]);
        static::assertCountViolations(2, $iterator, $rules);

        // invalid nesting
        $iterator = new ArrayIterator(['a', 'b']);
        static::assertCountViolations(2, $iterator, $rules);
    }

    /**
     * @throws Exception
     */
    public function testDoubleTraversableArray(): void
    {
        $rules = ['*.*' => 'required|int'];

        // empty array
        $iterator = new ArrayIterator([]);
        static::assertHasNoViolations($iterator, $rules);

        // empty nested array is not allowed
        $iterator = new ArrayIterator([[]]);
        static::assertHasViolations($iterator, $rules);

        // one entry: integer
        $iterator = new ArrayIterator([[5]]);
        static::assertHasNoViolations($iterator, $rules);

        // two entries: 2 integers
        $iterator = new ArrayIterator([[5, 6], [7, 8]]);
        static::assertHasNoViolations($iterator, $rules);

        // one entry: integer + string
        $iterator = new ArrayIterator([[5, 'test']]);
        static::assertHasViolations($iterator, $rules);

        // one entry: keys should be ignored
        $iterator = new ArrayIterator(['test' => [1]]);
        static::assertHasNoViolations($iterator, $rules);
    }
}
