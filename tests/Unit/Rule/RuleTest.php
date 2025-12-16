<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Unit\Rule;

use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use DigitalRevolution\SymfonyValidationShorthand\Rule\Rule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Rule::class)]
class RuleTest extends TestCase
{
    /**
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
     * @throws InvalidRuleException
     */
    public function testGetParameterInvalidIntType(): void
    {
        $this->expectException(InvalidRuleException::class);
        $this->expectExceptionMessage("Rule 'name' expects parameter #0 to be an int. Encountered: 'test'");

        $rule = new Rule('name', ['test']);
        $rule->getIntParam(0);
    }

    public function testGetName(): void
    {
        $rule = new Rule('name');
        static::assertSame('name', $rule->getName());
    }
}
