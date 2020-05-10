<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit;

use DigitalRevolution\SymfonyRequestValidation\RequestValidationRules;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\RequestValidationRules
 */
class ValidationRulesTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getQueryRules
     * @covers ::getRequestRules
     */
    public function testConstructorAndGetters(): void
    {
        $rules = new RequestValidationRules(['query' => 'a']);
        static::assertSame('a', $rules->getQueryRules());
        static::assertNull($rules->getRequestRules());

        $rules = new RequestValidationRules(['query' => 'a', 'request' => 'b']);
        static::assertSame('a', $rules->getQueryRules());
        static::assertSame('b', $rules->getRequestRules());
    }

    /**
     * @covers ::__construct
     */
    public function testEmptyConstructorArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new RequestValidationRules([]);
    }

    /**
     * @covers ::__construct
     */
    public function testInvalidPropertyArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new RequestValidationRules(['query' => 'a', 'b']);
    }
}
