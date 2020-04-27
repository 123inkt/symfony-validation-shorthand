<?php
declare(strict_types=1);

namespace Constraint;

use InvalidArgumentException;
use PrinsFrank\SymfonyRequestValidation\Constraint\ConstraintFactory;
use PHPUnit\Framework\TestCase;
use PrinsFrank\SymfonyRequestValidation\Rule\Parser\Rule;
use PrinsFrank\SymfonyRequestValidation\Rule\Parser\RuleSet;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @coversDefaultClass \PrinsFrank\SymfonyRequestValidation\Constraint\ConstraintFactory
 * @uses \PrinsFrank\SymfonyRequestValidation\Rule\Parser\RuleSet
 * @uses \PrinsFrank\SymfonyRequestValidation\Rule\Parser\Rule
 */
class ConstraintFactoryTest extends TestCase
{
    /**
     * @covers ::createFromRuleSet
     */
    public function testCreateFromRuleSetEmptyRuleSet(): void
    {
        static::assertSame([], ConstraintFactory::createFromRuleSet(new RuleSet()));
    }

    /**
     * @covers ::createFromRuleSet
     * @covers ::createFromRule
     */
    public function testCreateFromRuleSetInvalidRule(): void
    {
        $ruleSet = new RuleSet();
        $ruleSet->addRule(new Rule('unknown'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown rule');
        ConstraintFactory::createFromRuleSet($ruleSet);
    }

    /**
     * @covers ::createFromRuleSet
     * @covers ::createFromRule
     */
    public function testCreateFromRuleSet(): void
    {
        $ruleSet = new RuleSet();
        $ruleSet->addRule(new Rule('required'));
        $ruleSet->addRule(new Rule('max', ['5']));

        $constraints = ConstraintFactory::createFromRuleSet($ruleSet);
        static::assertCount(2, $constraints);
        static::assertInstanceOf(NotBlank::class, $constraints[0]);
        static::assertInstanceOf(Length::class, $constraints[1]);

        /** @var Length $constraint */
        $constraint = $constraints[1];
        static::assertSame(5, $constraint->max);
    }
}
