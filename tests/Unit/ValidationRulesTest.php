<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit;

use DigitalRevolution\SymfonyRequestValidation\RequestValidationRules;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

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
        $constraintA = new NotBlank();
        $constraintB = new NotNull();
        $rules       = new RequestValidationRules(['query' => $constraintA]);
        static::assertSame($constraintA, $rules->getQueryRules());
        static::assertNull($rules->getRequestRules());

        $rules = new RequestValidationRules(['query' => $constraintA, 'request' => $constraintB]);
        static::assertSame($constraintA, $rules->getQueryRules());
        static::assertSame($constraintB, $rules->getRequestRules());
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
