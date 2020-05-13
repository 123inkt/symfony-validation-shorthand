<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation;

use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintCollectionBuilder;
use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintMap;
use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintMapItem;
use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintResolver;
use DigitalRevolution\SymfonyRequestValidation\Constraint\Type\RequestConstraint;
use DigitalRevolution\SymfonyRequestValidation\Rule\RuleParser;
use DigitalRevolution\SymfonyRequestValidation\Utility\InvalidArrayPathException;
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
     * @throws RequestValidationException
     * @throws InvalidArrayPathException
     */
    public function createRequestConstraint(RequestValidationRules $validationRules): RequestConstraint
    {
        $options            = [];
        $queryDefinitions   = $validationRules->getQueryRules();
        $requestDefinitions = $validationRules->getRequestRules();

        if ($queryDefinitions !== null) {
            $options['query'] = $this->fromRuleDefinitions($queryDefinitions);
        }
        if ($requestDefinitions !== null) {
            $options['request'] = $this->fromRuleDefinitions($requestDefinitions);
        }

        return new RequestConstraint($options);
    }

    /**
     * @param Constraint|array<string, string|Constraint|array<string|Constraint>> $ruleDefinitions
     * @return Constraint|Constraint[]
     * @throws RequestValidationException
     * @throws InvalidArrayPathException
     */
    public function fromRuleDefinitions($ruleDefinitions)
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
        return $this->collectionBuilder->build($constraintMap);
    }
}
