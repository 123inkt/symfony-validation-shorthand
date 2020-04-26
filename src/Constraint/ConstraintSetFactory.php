<?php

namespace PrinsFrank\SymfonyRequestValidation\Constraint;

use PrinsFrank\SymfonyRequestValidation\Rule\Parser\RuleParser;
use PrinsFrank\SymfonyRequestValidation\Rule\Parser\StringReader;
use PrinsFrank\SymfonyRequestValidation\Rule\Parser\ValidationRuleParseException;
use PrinsFrank\SymfonyRequestValidation\Rule\RuleSet;
use Symfony\Component\Validator\Constraint;

class ConstraintSetFactory
{
    public static function createFromRuleset(RuleSet $ruleSet): ConstraintSet
    {


    }

    /**
     * @throws ValidationRuleParseException
     */
    private static function getConstraints(array $rules): array
    {
        $constraints = [];

        foreach ($rules as $rule) {
            if ($rule instanceof Constraint) {
                $constraints[] = $rule;
            }

            // convert string to RuleInfo[]
            $parsedRules = (new RuleParser(new StringReader($rule)))->parseRules();
        }

        return $constraints;
    }
}
