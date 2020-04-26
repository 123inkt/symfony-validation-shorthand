<?php

namespace PrinsFrank\SymfonyRequestValidation\Constraint;

use PrinsFrank\SymfonyRequestValidation\Rule\RuleSet;
use Symfony\Component\Validator\Constraint;

class ConstraintSetFactory
{
    public static function createFromRuleset(RuleSet $ruleSet): ConstraintSet
    {


    }

    private static function getConstraints(array $rules): array {
        $constraints = [];

        foreach($rules as $rule) {
            if ($rule instanceof Constraint) {
                $constraints[] = $rule;
            }


        }
    }
}
