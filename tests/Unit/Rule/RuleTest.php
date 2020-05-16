<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Unit\Rule;

use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use DigitalRevolution\SymfonyValidationShorthand\Rule\Rule;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyValidationShorthand\Rule\Rule
 * @covers ::__construct
 */
class RuleTest extends TestCase
{
    /**
     * @covers ::getParameter
     * @covers ::getParameters
     * @covers ::getIntParam
     * @throws InvalidRuleException
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
     * @throws InvalidRuleException
     */
    public function testGetParameterInvalidOffset(): void
    {
        $this->expectException(InvalidRuleException::class);
        $this->expectExceptionMessage("Rule 'name' expects at least 1 parameter(s)");

        $rule = new Rule('name', ['5']);
        $rule->getParameter(1);
    }

    /**
     * @covers ::getParameter
     * @covers ::getParameters
     * @covers ::getIntParam
     * @throws InvalidRuleException
     */
    public function testGetParameterInvalidIntType(): void
    {
        $this->expectException(InvalidRuleException::class);
        $this->expectExceptionMessage("Rule 'name' expects parameter #0 to be an int. Encountered: 'test'");

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
