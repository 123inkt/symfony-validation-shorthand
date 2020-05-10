<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Parser;

use DigitalRevolution\SymfonyRequestValidation\Parser\Rule;
use DigitalRevolution\SymfonyRequestValidation\Parser\RuleList;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Parser\RuleList
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

        $ruleSet = new RuleList();
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
        $ruleSet = new RuleList();
        static::assertFalse($ruleSet->hasRule('unit-test'));

        $ruleSet->addRule(new NotBlank());
        $ruleSet->addRule(new Rule('unit-test'));
        static::assertTrue($ruleSet->hasRule('unit-test'));
        static::assertFalse($ruleSet->hasRule('NotBlank'));
    }

    /**
     * @covers ::addRule
     */
    public function testAddRule(): void
    {
        $ruleA = new Rule('a');
        $ruleB = new Rule('b');

        $ruleSet = new RuleList();
        static::assertCount(0, $ruleSet);

        // add a rule
        $ruleSet->addRule($ruleA);
        static::assertSame([$ruleA], $ruleSet->getRules());

        $ruleSet->addRule($ruleB);
        static::assertSame([$ruleA, $ruleB], $ruleSet->getRules());
    }
}
