<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Builder;

use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintMap;
use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintResolver;
use DigitalRevolution\SymfonyRequestValidation\Parser\RuleListMap;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;

class ConstraintMapBuilder
{
    /** @var ConstraintResolver */
    private $resolver;

    public function __construct(ConstraintResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @throws RequestValidationException
     */
    public function build(RuleListMap $ruleListMap): ConstraintMap
    {
        $constraintMap = new ConstraintMap();
        foreach ($ruleListMap as $key => $ruleList) {
            $constraintMap->set($key, $this->resolver->resolveRuleList($ruleList));
        }

        return $constraintMap;
    }
}
