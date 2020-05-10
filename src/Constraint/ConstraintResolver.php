<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use DigitalRevolution\SymfonyRequestValidation\Parser\Rule;
use DigitalRevolution\SymfonyRequestValidation\Parser\RuleList;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
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
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Url;

class ConstraintResolver
{
    /**
     * @throws RequestValidationException
     */
    public function resolveRuleList(RuleList $ruleList): Constraint
    {
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
            $constraints[] = new NotNull();
        }

        if ($required === false) {
            return new Optional($constraints);
        }
        return new Required($constraints);
    }

    /**
     * @throws RequestValidationException
     */
    protected function resolveConstraint(RuleList $ruleList, Rule $rule): Constraint
    {
        switch ($rule->getName()) {
            case Rule::RULE_BOOLEAN:
                return new Boolean();
            case Rule::RULE_INTEGER:
                return new IntegerNumber();
            case Rule::RULE_FLOAT:
                return new FloatNumber();
            case Rule::RULE_STRING:
                return new Type('string');
            case Rule::RULE_EMAIL:
                return new Email();
            case Rule::RULE_URL:
                return new Url();
            case Rule::RULE_REGEX:
                return new Regex(['pattern' => $rule->getParameter(0)]);
            case Rule::RULE_FILLED:
                return new NotBlank(['allowNull' => $ruleList->hasRule(Rule::RULE_NULLABLE)]);
            case Rule::RULE_MIN:
                if ($ruleList->hasRule([Rule::RULE_INTEGER, Rule::RULE_FLOAT])) {
                    return new GreaterThanOrEqual($rule->getIntParam(0));
                }
                return new Length(['min' => $rule->getIntParam(0)]);
            case Rule::RULE_MAX:
                if ($ruleList->hasRule([Rule::RULE_INTEGER, Rule::RULE_FLOAT])) {
                    return new LessThanOrEqual($rule->getIntParam(0));
                }
                return new Length(['max' => $rule->getIntParam(0)]);
            case Rule::RULE_BETWEEN:
                if ($ruleList->hasRule([Rule::RULE_INTEGER, Rule::RULE_FLOAT])) {
                    return new Range(['min' => $rule->getIntParam(0), 'max' => $rule->getIntParam(1)]);
                }
                return new Length(['min' => $rule->getIntParam(0), 'max' => $rule->getIntParam(1)]);
        }

        throw new RequestValidationException(
            'Unable to resolve rule: ' . $rule->getName() . '. Supported rules: ' . implode(", ", Rule::ALLOWED_RULES)
        );
    }
}
