<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use DigitalRevolution\SymfonyRequestValidation\Parser\Rule;
use DigitalRevolution\SymfonyRequestValidation\Parser\RuleSet;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use DigitalRevolution\SymfonyRequestValidation\Validator\Constraint\Boolean;
use DigitalRevolution\SymfonyRequestValidation\Validator\Constraint\FloatNumber;
use DigitalRevolution\SymfonyRequestValidation\Validator\Constraint\IntegerNumber;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Url;

class ConstraintResolver
{
    /**
     * @return Constraint|Constraint[]
     * @throws RequestValidationException
     */
    public function resolveRuleSet(RuleSet $ruleSet)
    {
        $required    = false;
        $nullable    = false;
        $constraints = [];
        foreach ($ruleSet->getRules() as $rule) {
            if ($rule instanceof Constraint) {
                $constraints[] = $rule;
                continue;
            }

            /** @var Rule $rule */
            if ($rule->getName() === Rule::RULE_REQUIRED) {
                $required = true;
                continue;
            }

            if ($rule->getName() === Rule::RULE_NULLABLE) {
                $nullable = true;
                continue;
            }

            $constraints[] = $this->resolveConstraint($ruleSet, $rule);
        }

        if ($nullable === false) {
            $constraints[] = new NotNull();
        }

        if ($required === false) {
            return new Optional($constraints);
        }
        return $constraints;
    }

    /**
     * @throws RequestValidationException
     */
    protected function resolveConstraint(RuleSet $ruleSet, Rule $rule): Constraint
    {
        switch ($rule->getName()) {
            case Rule::RULE_BOOLEAN:
                return new Boolean(['allowNull' => $ruleSet->hasRule(Rule::RULE_NULLABLE)]);
            case Rule::RULE_INTEGER:
                return new IntegerNumber(['allowNull' => $ruleSet->hasRule(Rule::RULE_NULLABLE)]);
            case Rule::RULE_FLOAT:
                return new FloatNumber(['allowNull' => $ruleSet->hasRule(Rule::RULE_NULLABLE)]);
            case Rule::RULE_STRING:
                return new Type('string');
            case Rule::RULE_EMAIL:
                return new Email();
            case Rule::RULE_URL:
                return new Url();
            case Rule::RULE_REGEX:
                return new Regex(['pattern' => $rule->getParameter(0)]);
            case Rule::RULE_FILLED:
                return new NotBlank(['allowNull' => $ruleSet->hasRule(Rule::RULE_NULLABLE)]);
            case Rule::RULE_MIN:
                if ($ruleSet->hasRule(Rule::RULE_INTEGER)) {
                    return new GreaterThanOrEqual($rule->getIntParam(0));
                }
                return new Length(['min' => $rule->getIntParam(0)]);
            case Rule::RULE_MAX:
                if ($ruleSet->hasRule(Rule::RULE_INTEGER)) {
                    return new LessThanOrEqual($rule->getIntParam(0));
                }
                return new Length(['max' => $rule->getIntParam(0)]);
            case Rule::RULE_BETWEEN:
                if ($ruleSet->hasRule(Rule::RULE_INTEGER)) {
                    return new Range(['min' => $rule->getIntParam(0), 'max' => $rule->getIntParam(1)]);
                }
                return new Length(['min' => $rule->getIntParam(0), 'max' => $rule->getIntParam(1)]);
        }

        throw new RequestValidationException(
            'Unable to resolve rule: ' . $rule->getName() . '. Supported rules: ' . implode(", ", Rule::ALLOWED_RULES)
        );
    }
}
