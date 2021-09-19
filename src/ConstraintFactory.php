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
    /** @var RuleParser */
    private $parser;

    /** @var ConstraintResolver */
    private $resolver;

    /** @var ConstraintCollectionBuilder */
    private $collectionBuilder;

    public function __construct(RuleParser $parser = null, ConstraintResolver $resolver = null, ConstraintCollectionBuilder $collectionBuilder = null)
    {
        $this->parser            = $parser ?? new RuleParser();
        $this->resolver          = $resolver ?? new ConstraintResolver();
        $this->collectionBuilder = $collectionBuilder ?? new ConstraintCollectionBuilder();
    }

    /**
     * @param Constraint|array<string, string|Constraint|array<string|Constraint>> $ruleDefinitions
     * @return Constraint|Constraint[]
     * @throws InvalidRuleException
     */
    public function fromRuleDefinitions($ruleDefinitions, bool $allowExtraFields = false)
    {
        if ($ruleDefinitions instanceof Constraint) {
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
        return $this->collectionBuilder->build($constraintMap, $allowExtraFields);
    }
}
