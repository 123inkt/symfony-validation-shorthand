<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation;

use DigitalRevolution\SymfonyRequestValidation\Builder\MapBuilderFactory;
use DigitalRevolution\SymfonyRequestValidation\Builder\MapBuilderFactoryInterface;
use DigitalRevolution\SymfonyRequestValidation\Constraint\Type\RequestConstraint;
use DigitalRevolution\SymfonyRequestValidation\Utility\InvalidArrayPathException;
use DigitalRevolution\SymfonyRequestValidation\Validator\DataValidator;
use DigitalRevolution\SymfonyRequestValidation\Validator\RequestValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ConstraintFactory
{
    /** @var MapBuilderFactoryInterface */
    private $factory;

    public function __construct(MapBuilderFactoryInterface $factory = null)
    {
        $this->factory = $factory ?? new MapBuilderFactory();
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
            $options['queryConstraint'] = $this->createConstraintFromDefinition($queryDefinitions);
        }
        if ($requestDefinitions !== null) {
            $options['requestConstraint'] = $this->createConstraintFromDefinition($requestDefinitions);
        }

        return new RequestConstraint($options);
    }

    /**
     * @param Assert\Collection|array<string, string|Constraint|array<string|Constraint>> $ruleDefinitions
     * @throws RequestValidationException
     * @throws InvalidArrayPathException
     */
    public function createConstraintFromDefinition($ruleDefinitions): Constraint
    {
        if ($ruleDefinitions instanceof Constraint) {
            return $ruleDefinitions;
        }

        $ruleList      = $this->factory->createRuleListMapBuilder()->build($ruleDefinitions);
        $constraintMap = $this->factory->createConstraintMapBuilder()->build($ruleList);
        return $this->factory->createConstraintCollectionBuilder()->build($constraintMap);
    }
}
