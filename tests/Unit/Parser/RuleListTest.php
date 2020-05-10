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
class RuleListTest extends TestCase
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

        $ruleList = new RuleList();
        static::assertCount(0, $ruleList);

        // add rule on empty set
        $ruleList->addAll([$ruleA, $ruleB]);
        static::assertCount(2, $ruleList);
        static::assertSame([$ruleA, $ruleB], $ruleList->getRules());

        // add more to existing set
        $ruleList->addAll([$ruleC]);
        static::assertCount(3, $ruleList);
        static::assertSame([$ruleA, $ruleB, $ruleC], $ruleList->getRules());
    }

    /**
     * @covers ::hasRule
     */
    public function testHasRule(): void
    {
        $ruleList = new RuleList();
        static::assertFalse($ruleList->hasRule('unit-test'));

        $ruleList->addRule(new NotBlank());
        $ruleList->addRule(new Rule('unit-test'));
        static::assertTrue($ruleList->hasRule('unit-test'));
        static::assertFalse($ruleList->hasRule('NotBlank'));
    }

    /**
     * @covers ::addRule
     */
    public function testAddRule(): void
    {
        $ruleA = new Rule('a');
        $ruleB = new Rule('b');

        $ruleList = new RuleList();
        static::assertCount(0, $ruleList);

        // add a rule
        $ruleList->addRule($ruleA);
        static::assertSame([$ruleA], $ruleList->getRules());

        $ruleList->addRule($ruleB);
        static::assertSame([$ruleA, $ruleB], $ruleList->getRules());
    }
}
