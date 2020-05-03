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

    public function mockResolveRuleSet(RuleSet $ruleSet, Constraint $constraint, int $expects = 1): self
    {
        $this->resolver
            ->expects(new InvokedCountMatcher($expects))
            ->method('resolveRuleSet')
            ->with($ruleSet)
            ->willReturn($constraint);

        return $this;
    }
}
