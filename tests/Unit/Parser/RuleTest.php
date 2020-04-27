<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Parser;

use DigitalRevolution\SymfonyRequestValidation\Parser\Rule;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Parser\Rule
 * @covers ::__construct
 */
class RuleTest extends TestCase
{
    /**
     * @covers ::getParameter
     * @covers ::getParameters
     * @covers ::getIntParam
     */
    public function testGetParameter(): void
    {
        $rule = new Rule('name');
        static::assertSame([], $rule->getParameters());

        $rule = new Rule('name', ['5']);
        static::assertSame('5', $rule->getParameter(0));
        static::assertSame(5, $rule->getIntParam(0));
        static::assertSame(['5'], $rule->getParameters());
    }

    /**
     * @covers ::getParameter
     * @covers ::getParameters
     * @covers ::getIntParam
     */
    public function testGetParameterInvalidOffset(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown offset for rule');

        $rule = new Rule('name', ['5']);
        $rule->getParameter(1);
    }

    /**
     * @covers ::getParameter
     * @covers ::getParameters
     * @covers ::getIntParam
     */
    public function testGetParameterInvalidIntType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid int argument for rule');

        $rule = new Rule('name', ['test']);
        $rule->getIntParam(0);
    }

    /**
     * @covers ::getName
     */
    public function testGetName(): void
    {
        $rule = new Rule('name');
        static::assertSame('name', $rule->getName());
    }
}
