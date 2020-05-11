<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation;

use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintCollectionBuilder;
use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintMap;
use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintResolver;
use DigitalRevolution\SymfonyRequestValidation\Constraint\Type\RequestConstraint;
use DigitalRevolution\SymfonyRequestValidation\Rule\RuleParser;
use DigitalRevolution\SymfonyRequestValidation\Utility\InvalidArrayPathException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class ConstraintFactory
{
    /** @var RuleParser */
    private $parser;

    /** @var ConstraintResolver */
    private $resolver;

    /** @var ConstraintCollectionBuilder */
    private $collectionBuilder;

    public function __construct(
        RuleParser $parser = null,
        ConstraintResolver $resolver = null,
        ConstraintCollectionBuilder $collectionBuilder = null
    ) {
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
            $options['queryConstraint'] = $this->createConstraintFromDefinitions($queryDefinitions);
        }
        if ($requestDefinitions !== null) {
            $options['requestConstraint'] = $this->createConstraintFromDefinitions($requestDefinitions);
        }

        return new RequestConstraint($options);
    }

    /**
     * @param Assert\Collection|array<string, string|Constraint|array<string|Constraint>> $ruleDefinitions
     * @throws RequestValidationException
     * @throws InvalidArrayPathException
     */
    public function createConstraintFromDefinitions($ruleDefinitions): Constraint
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
            $constraint = $this->resolver->resolveRuleList($ruleList);

            // add to set
            $constraintMap->set($key, $constraint);
        }

        // transform ConstraintMap to ConstraintCollection
        return $this->collectionBuilder->build($constraintMap);
    }
}
