<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use DigitalRevolution\SymfonyRequestValidation\Rule\Rule;
use DigitalRevolution\SymfonyRequestValidation\Rule\RuleList;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class ConstraintResolver
{
    /**
     * @throws RequestValidationException
     */
    public function resolveRuleList(RuleList $ruleList): Constraint
    {
        // all Constraints, return early
        if ($ruleList->hasRules() === false) {
            return new Assert\Required($ruleList->getRules());
        }

        $required    = false;
        $nullable    = false;
        $constraints = [];
        foreach ($ruleList->getRules() as $rule) {
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

            $constraints[] = $this->resolveConstraint($ruleList, $rule);
        }

        if ($nullable === false) {
            $constraints[] = new Assert\NotNull();
        }

        if ($required === false) {
            return new Assert\Optional($constraints);
        }
        return new Assert\Required($constraints);
    }

    /**
     * @throws RequestValidationException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function resolveConstraint(RuleList $ruleList, Rule $rule): Constraint
    {
        switch ($rule->getName()) {
            case Rule::RULE_BOOLEAN:
                return new Type\Boolean();
            case Rule::RULE_INTEGER:
                return new Type\IntegerNumber();
            case Rule::RULE_FLOAT:
                return new Type\FloatNumber();
            case Rule::RULE_STRING:
                return new Assert\Type('string');
            case Rule::RULE_EMAIL:
                return new Assert\Email();
            case Rule::RULE_URL:
                return new Assert\Url();
            case Rule::RULE_REGEX:
                return new Assert\Regex(['pattern' => $rule->getParameter(0)]);
            case Rule::RULE_FILLED:
                return new Assert\NotBlank(['allowNull' => $ruleList->hasRule(Rule::RULE_NULLABLE)]);
            case Rule::RULE_MIN:
                return $this->resolveMinConstraint($rule, $ruleList->hasRule([Rule::RULE_INTEGER, Rule::RULE_FLOAT]));
            case Rule::RULE_MAX:
                return $this->resolveMaxConstraint($rule, $ruleList->hasRule([Rule::RULE_INTEGER, Rule::RULE_FLOAT]));
            case Rule::RULE_BETWEEN:
                return $this->resolveBetweenConstraint($rule, $ruleList->hasRule([Rule::RULE_INTEGER, Rule::RULE_FLOAT]));
        }

        throw new RequestValidationException(
            'Unable to resolve rule: ' . $rule->getName() . '. Supported rules: ' . implode(", ", Rule::ALLOWED_RULES)
        );
    }

    /**
     * @throws RequestValidationException
     */
    private function resolveMinConstraint(Rule $rule, bool $isNumeric): Constraint
    {
        if ($isNumeric) {
            return new Assert\GreaterThanOrEqual($rule->getIntParam(0));
        }
        return new Assert\Length(['min' => $rule->getIntParam(0)]);
    }

    /**
     * @throws RequestValidationException
     */
    private function resolveMaxConstraint(Rule $rule, bool $isNumeric): Constraint
    {
        if ($isNumeric) {
            return new Assert\LessThanOrEqual($rule->getIntParam(0));
        }
        return new Assert\Length(['max' => $rule->getIntParam(0)]);
    }

    /**
     * @throws RequestValidationException
     */
    private function resolveBetweenConstraint(Rule $rule, bool $isNumeric): Constraint
    {
        if ($isNumeric) {
            return new Assert\Range(['min' => $rule->getIntParam(0), 'max' => $rule->getIntParam(1)]);
        }
        return new Assert\Length(['min' => $rule->getIntParam(0), 'max' => $rule->getIntParam(1)]);
    }
}
