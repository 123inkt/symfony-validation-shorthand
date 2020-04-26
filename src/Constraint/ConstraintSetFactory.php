<?php

declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation\Constraint;

use PrinsFrank\SymfonyRequestValidation\Rule\Parser\RuleParser;
use PrinsFrank\SymfonyRequestValidation\Rule\Parser\StringReader;
use PrinsFrank\SymfonyRequestValidation\Rule\Parser\ValidationRuleParseException;
use PrinsFrank\SymfonyRequestValidation\Rule\RuleSet;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Required;

class ConstraintSetFactory
{
    /**
     * @throws ValidationRuleParseException
     */
    public static function createFromRuleset(RuleSet $ruleSet): ConstraintSet
    {
        $constraintSet = new ConstraintSet();
        $constraintSet->setQueryConstraints(self::getConstraintForRule($ruleSet->getQueryRules()));
        $constraintSet->setRequestConstraints(self::getConstraintForRule($ruleSet->getRequestRules()));

        return $constraintSet;
    }

    /**
     * @throws ValidationRuleParseException
     */
    private static function getConstraintForRule(?array $fieldRules): ?Constraint
    {
        if ($fieldRules === null) {
            return null;
        }

        $fields = [];
        foreach ($fieldRules as $fieldName => $rules) {
            $fields[$fieldName] = self::getConstraints($rules);
        }

        return new Collection(['fields' => $fields]);
    }

    /**
     * @throws ValidationRuleParseException
     */
    private static function getConstraints(array $rules): Constraint
    {
        $isRequired = false;
        $constraints = [];
        foreach ($rules as $rule) {
            if ($rule instanceof Constraint) {
                $constraints[] = [$rule];
                continue;
            }

            // convert string to RuleSet[]
            $ruleSet = (new RuleParser(new StringReader($rule)))->parseRules();
            $isRequired = $isRequired || $ruleSet->isRequired();

            // convert RuleSet to Constraints
            $constraints[] = ConstraintFactory::createFromRuleSet($ruleSet);
        }

        $constraints = array_merge(...$constraints);
        if ($isRequired) {
            return new Required($constraints);
        }

        return new Optional($constraints);
    }
}
