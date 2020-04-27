<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Parser;

use DigitalRevolution\SymfonyRequestValidation\Parser\Rule;
use DigitalRevolution\SymfonyRequestValidation\Parser\RuleSet;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Parser\RuleSet
 */
class RuleSetTest extends TestCase
{
    /**
     * @covers ::addAll
     * @covers ::getRules
     * @covers ::count
     */
    public function testAddAll(): void
    {
        $ruleA = new Rule('a');
        $ruleB = new Rule('b');
        $ruleC = new Rule('c');

        $ruleSet = new RuleSet();
        static::assertCount(0, $ruleSet);

        // add rule on empty set
        $ruleSet->addAll([$ruleA, $ruleB]);
        static::assertCount(2, $ruleSet);
        static::assertSame([$ruleA, $ruleB], $ruleSet->getRules());

        // add more to existing set
        $ruleSet->addAll([$ruleC]);
        static::assertCount(3, $ruleSet);
        static::assertSame([$ruleA, $ruleB, $ruleC], $ruleSet->getRules());
    }

    /**
     * @covers ::hasRule
     */
    public function testHasRule(): void
    {
        $ruleSet = new RuleSet();
        static::assertFalse($ruleSet->hasRule('unit-test'));

        $ruleSet->addRule(new NotBlank());
        $ruleSet->addRule(new Rule('unit-test'));
        static::assertTrue($ruleSet->hasRule('unit-test'));
        static::assertFalse($ruleSet->hasRule('NotBlank'));
    }

    /**
     * @covers ::setRules
     * @covers ::getRules
     */
    public function testSetRules(): void
    {
        $rule    = new Rule('a');
        $ruleSet = new RuleSet();
        static::assertSame([$rule], $ruleSet->setRules([$rule])->getRules());
    }

    /**
     * @covers ::addRule
     * @covers ::setRules
     */
    public function testAddRule(): void
    {
        $ruleA = new Rule('a');
        $ruleB = new Rule('b');
        $ruleC = new Rule('C');

        $ruleSet = new RuleSet();
        static::assertCount(0, $ruleSet);

        // add a rule
        $ruleSet->addRule($ruleA);
        static::assertSame([$ruleA], $ruleSet->getRules());

        // set rules, should overwrite
        $ruleSet->setRules([$ruleC, $ruleB]);
        static::assertSame([$ruleC, $ruleB], $ruleSet->getRules());

        $ruleSet->addRule($ruleA);
        static::assertSame([$ruleC, $ruleB, $ruleA], $ruleSet->getRules());
    }
}
