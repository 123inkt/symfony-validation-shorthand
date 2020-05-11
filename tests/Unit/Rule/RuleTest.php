<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Rule;

use DigitalRevolution\SymfonyRequestValidation\Rule\Rule;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Rule\Rule
 * @covers ::__construct
 */
class RuleTest extends TestCase
{
    /**
     * @covers ::getParameter
     * @covers ::getParameters
     * @covers ::getIntParam
     * @throws RequestValidationException
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
     * @throws RequestValidationException
     */
    public function testGetParameterInvalidOffset(): void
    {
        $this->expectException(RequestValidationException::class);
        $this->expectExceptionMessage('Rule `name` expects at least 1 parameter(s)');

        $rule = new Rule('name', ['5']);
        $rule->getParameter(1);
    }

    /**
     * @covers ::getParameter
     * @covers ::getParameters
     * @covers ::getIntParam
     * @throws RequestValidationException
     */
    public function testGetParameterInvalidIntType(): void
    {
        $this->expectException(RequestValidationException::class);
        $this->expectExceptionMessage('Rule `name` expects parameter #0 to be an int. Encountered: `test`');

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
