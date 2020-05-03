<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Mock;

use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintResolver;
use DigitalRevolution\SymfonyRequestValidation\Parser\RuleSet;
use PHPUnit\Framework\MockObject\Matcher\InvokedCount as InvokedCountMatcher;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraint;

class ConstraintResolverMockHelper
{
    /** @var ConstraintResolver|MockObject */
    private $resolver;

    /**
     * @param ConstraintResolver|MockObject $resolver
     */
    public function __construct($resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param RuleSet|RuleSet[] $ruleSets
     * @param Constraint|Constraint[] $constraints
     */
    public function mockResolveRuleSet($ruleSets, $constraints, int $expects = 1): self
    {
        if (is_array($ruleSets) === false) {
            $ruleSets = [[$ruleSets]];
        } else {
            // transform $ruleSets to an array of arrays to support the spread operator for withConsecutive
            $result = [];
            foreach ($ruleSets as $ruleSet) {
                $result[] = [$ruleSet];
            }
            $ruleSets = $result;
        }
        if (is_array($constraints) === false) {
            $constraints = [$constraints];
        }
        $this->resolver
            ->expects(new InvokedCountMatcher($expects))
            ->method('resolveRuleSet')
            ->withConsecutive(...$ruleSets)
            ->willReturn(...$constraints);

        return $this;
    }
}
