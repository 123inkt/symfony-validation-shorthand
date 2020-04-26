<?php

declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation\Constraint;

use PrinsFrank\SymfonyRequestValidation\Rule\Parser\RuleInfo;
use Symfony\Component\Validator\Constraint;

class ConstraintFactory
{
    public function fromRuleInfo(RuleInfo $ruleInfo): Constraint
    {
    }
}
