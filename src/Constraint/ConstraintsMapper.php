<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use DigitalRevolution\SymfonyRequestValidation\Parser\RuleListMap;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;

class ConstraintsMapper
{
    /** @var RuleListMap */
    private $ruleListMap;

    /** @var ConstraintResolver */
    private $resolver;

    public function __construct(RuleListMap $ruleListMap, ?ConstraintResolver $resolver)
    {
        $this->ruleListMap = $ruleListMap;
        $this->resolver    = $resolver ?? new ConstraintResolver();
    }

    /**
     * @throws RequestValidationException
     */
    public function createConstraintMap(): ConstraintMap
    {
        $constraintMap = new ConstraintMap();
        foreach ($this->ruleListMap as $key => $ruleList) {
            $constraintMap->set($key, $this->resolver->resolveRuleList($ruleList));
        }

        return $constraintMap;
    }
}
