<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Constraint;

use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use DigitalRevolution\SymfonyValidationShorthand\Rule\Rule;
use DigitalRevolution\SymfonyValidationShorthand\Rule\RuleList;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class ConstraintResolver
{
    /**
     * @return Constraint[]
     * @throws InvalidRuleException
     */
    public function resolveRuleList(RuleList $ruleList): array
    {
        // all Constraints, return early
        if ($ruleList->hasRules() === false) {
            /** @var Constraint[] $constraints */
            $constraints = $ruleList->getRules();

            return $constraints;
        }

        $nullable    = false;
        $constraints = [];
        foreach ($ruleList->getRules() as $rule) {
            if ($rule instanceof Constraint) {
                $constraints[] = $rule;
                continue;
            }

            /** @var Rule $rule */
            if ($rule->getName() === Rule::RULE_REQUIRED) {
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

        return $constraints;
    }

    /**
     * @throws InvalidRuleException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function resolveConstraint(RuleList $ruleList, Rule $rule): Constraint
    {
        switch ($rule->getName()) {
            case Rule::RULE_BOOLEAN:
                return new Type\BooleanValue();
            case Rule::RULE_INTEGER:
                return new Type\IntegerNumber();
            case Rule::RULE_FLOAT:
                return new Type\FloatNumber();
            case Rule::RULE_STRING:
                return new Assert\Type('string');
            case Rule::RULE_ARRAY:
                return new Assert\Type('array');
            case Rule::RULE_ALPHA:
                return new Assert\Regex(['pattern' => '/^[a-zA-Z]*$/']);
            case Rule::RULE_ALPHA_DASH:
                return new Assert\Regex(['pattern' => '/^[\w-]*$/']);
            case Rule::RULE_ALPHA_NUM:
                return new Assert\Regex(['pattern' => '/^[a-zA-Z0-9]*$/']);
            case Rule::RULE_IN:
                return new Type\In(['values', $rule->getParameters()]);
            case Rule::RULE_DATE:
                return new Assert\Date();
            case Rule::RULE_DATETIME:
                return new Assert\DateTime();
            case Rule::RULE_DATE_FORMAT:
                return new Assert\DateTime(['format' => $rule->getParameter(0)]);
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

        throw new InvalidRuleException(
            "Unable to resolve rule: '" . $rule->getName() . "'. Supported rules: " . implode(", ", Rule::ALLOWED_RULES)
        );
    }

    /**
     * @throws InvalidRuleException
     */
    private function resolveMinConstraint(Rule $rule, bool $isNumeric): Constraint
    {
        if ($isNumeric) {
            return new Assert\GreaterThanOrEqual($rule->getIntParam(0));
        }
        return new Assert\Length(['min' => $rule->getIntParam(0)]);
    }

    /**
     * @throws InvalidRuleException
     */
    private function resolveMaxConstraint(Rule $rule, bool $isNumeric): Constraint
    {
        if ($isNumeric) {
            return new Assert\LessThanOrEqual($rule->getIntParam(0));
        }
        return new Assert\Length(['max' => $rule->getIntParam(0)]);
    }

    /**
     * @throws InvalidRuleException
     */
    private function resolveBetweenConstraint(Rule $rule, bool $isNumeric): Constraint
    {
        if ($isNumeric) {
            return new Assert\Range(['min' => $rule->getIntParam(0), 'max' => $rule->getIntParam(1)]);
        }
        return new Assert\Length(['min' => $rule->getIntParam(0), 'max' => $rule->getIntParam(1)]);
    }
}
