<?php

declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation\Constraint;

use InvalidArgumentException;
use PrinsFrank\SymfonyRequestValidation\Rule\Parser\Rule;
use PrinsFrank\SymfonyRequestValidation\Rule\Parser\RuleSet;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ConstraintFactory
{
    /**
     * @return Constraint[]
     */
    public static function createFromRuleSet(RuleSet $ruleSet): array
    {
        $constraints = [];
        foreach ($ruleSet->getRules() as $rule) {
            $constraints[] = self::createFromRule($rule);
        }

        return $constraints;
    }

    private static function createFromRule(Rule $rule): Constraint
    {
        switch ($rule->getName()) {
            case 'required':
                return new NotBlank();
            case 'max':
                return new Length(['max' => $rule->getIntArgument(0)]);
        }

        throw new InvalidArgumentException('Unknown rule: ' . $rule->getName());
    }
}
