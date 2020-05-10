<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Utility;

use DigitalRevolution\SymfonyRequestValidation\Utility\Arrays;
use DigitalRevolution\SymfonyRequestValidation\Utility\InvalidArrayPathException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Utility\Arrays
 */
class ArraysTest extends TestCase
{
    /**
     *
     * @throws InvalidArrayPathException
     */
    public function testAssignToPath(): void
    {
        $data = ['a' => []];

        static::assertSame(['a' => ['c']], Arrays::assignToPath($data, ['a', '0'], 'c'));
    }
}
