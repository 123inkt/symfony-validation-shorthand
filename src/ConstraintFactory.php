<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand;

use DigitalRevolution\SymfonyValidationShorthand\Constraint\ConstraintCollectionBuilder;
use DigitalRevolution\SymfonyValidationShorthand\Constraint\ConstraintMap;
use DigitalRevolution\SymfonyValidationShorthand\Constraint\ConstraintMapItem;
use DigitalRevolution\SymfonyValidationShorthand\Constraint\ConstraintResolver;
use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use DigitalRevolution\SymfonyValidationShorthand\Rule\RuleParser;
use Symfony\Component\Validator\Constraint;

class ConstraintFactory
{
    public function __construct(
        private readonly RuleParser $parser = new RuleParser(),
        private readonly ConstraintResolver $resolver = new ConstraintResolver(),
        private readonly ConstraintCollectionBuilder $collectionBuilder = new ConstraintCollectionBuilder()
    ) {
    }

    /**
     * @param Constraint|array<string|Constraint|array<string|Constraint>> $ruleDefinitions
     * @param bool                                                         $allowExtraFields Allow for extra, unvalidated, fields to be
     *
     * @return Constraint|Constraint[]
     * @throws InvalidRuleException
     */
    public function fromRuleDefinitions(Constraint|array $ruleDefinitions, bool $allowExtraFields = false): Constraint|array
    {
        if ($ruleDefinitions instanceof Constraint || self::isConstraintList($ruleDefinitions)) {
            return $ruleDefinitions;
        }

        // transform rule definitions to ConstraintMap
        $constraintMap = new ConstraintMap();
        foreach ($ruleDefinitions as $key => $rules) {
            // transform rules to RuleList
            $ruleList = $this->parser->parseRules($rules);

            // transform RuleList to ConstraintMap
            $constraints = $this->resolver->resolveRuleList($ruleList);

            // add to set
            $constraintMap->set($key, new ConstraintMapItem($constraints, $ruleList->hasRule('required')));
        }

        // transform ConstraintMap to ConstraintCollection
        return $this->collectionBuilder->setAllowExtraFields($allowExtraFields)->build($constraintMap);
    }

    /**
     * Check if `definition` is of type `array<int, Constraint>`
     *
     * @param array<string|Constraint|array<string|Constraint>> $ruleDefinitions
     *
     * @phpstan-assert-if-true Constraint[]                     $ruleDefinitions
     */
    private static function isConstraintList(array $ruleDefinitions): bool
    {
        foreach ($ruleDefinitions as $key => $definition) {
            if (is_int($key) === false || $definition instanceof Constraint === false) {
                return false;
            }
        }

        return true;
    }
}
