<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use DigitalRevolution\SymfonyRequestValidation\Parser\Rule;
use DigitalRevolution\SymfonyRequestValidation\Parser\RuleSet;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;

class ConstraintResolver
{
    public function resolveRuleSet(RuleSet $ruleSet)
    {
        $required    = false;
        $constraints = [];
        foreach ($ruleSet->getRules() as $rule) {
            if ($rule instanceof Constraint) {
                $constraints[] = $rule;
                continue;
            }

            /** @var Rule $rule */
            if ($rule->getName() === 'required') {
                $required = true;
                continue;
            }

            $constraints[] = $this->resolveConstraint($ruleSet, $rule);
        }

        if ($required) {
            return new Required($constraints);
        }
        return new Optional($constraints);
    }


    protected function resolveConstraint(RuleSet $ruleSet, Rule $rule): Constraint
    {
        switch ($rule->getName()) {
            case 'boolean':
                return new Type('bool');
            case 'integer':
                return new Type('integer');
            case 'float':
                return new Type('float');
            case 'email':
                return new Email();
            case 'min':
                if ($ruleSet->hasRule('integer')) {
                    return new Range(['min' => $rule->getIntParam(0)]);
                }
                return new Length(['min' => $rule->getIntParam(0)]);
            case 'max':
                if ($ruleSet->hasRule('integer')) {
                    return new Range(['max' => $rule->getIntParam(0)]);
                }
                return new Length(['max' => $rule->getIntParam(0)]);
            case 'between':
                if ($ruleSet->hasRule('integer')) {
                    return new Range(['min' => $rule->getIntParam(0), 'max' => $rule->getIntParam(1)]);
                }
                return new Length(['min' => $rule->getIntParam(0), 'max' => $rule->getIntParam(1)]);
        }

        throw new InvalidArgumentException('Unable to resolve rule: ' . $rule->getName());
    }
}
