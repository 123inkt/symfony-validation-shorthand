<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation;

use DigitalRevolution\SymfonyRequestValidation\Builder\MapBuilderFactoryInterface;
use DigitalRevolution\SymfonyRequestValidation\Constraint\Type\RequestConstraint;
use DigitalRevolution\SymfonyRequestValidation\Constraint\Type\TraversableConstraint;
use DigitalRevolution\SymfonyRequestValidation\Validator\ArrayValidator;
use DigitalRevolution\SymfonyRequestValidation\Validator\RequestValidator;
use DigitalRevolution\SymfonyRequestValidation\Validator\TraversableDataValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DataValidatorFactory
{
    /** @var MapBuilderFactoryInterface */
    private $factory;
    /** @var ValidatorInterface */
    private $validator;


    public function __construct(MapBuilderFactoryInterface $factory, ValidatorInterface $validator = null)
    {
        $this->factory   = $factory;
        $this->validator = $validator ?? Validation::createValidator();
    }

    /**
     * @param Collection|array<string, string|Constraint|array<string|Constraint>>|null $queryDefinitions
     * @param Collection|array<string, string|Constraint|array<string|Constraint>>|null $requestDefinitions
     * @throws RequestValidationException
     * @throws Utility\InvalidArrayPathException
     */
    public function createRequestValidator($queryDefinitions, $requestDefinitions): RequestValidator
    {
        $options = [
            'queryConstraint'   => null,
            'requestConstraint' => null
        ];

        if ($queryDefinitions !== null) {
            $options['queryConstraint'] = $this->getConstraint($queryDefinitions);
        }
        if ($requestDefinitions !== null) {
            $options['requestConstraint'] = $this->getConstraint($requestDefinitions);
        }

        return new RequestValidator(new RequestConstraint($options), $this->validator);
    }

    /**
     * @param Collection|array<string, string|Constraint|array<string|Constraint>> $ruleDefinitions
     * @throws RequestValidationException
     * @throws Utility\InvalidArrayPathException
     */
    public function createArrayValidator($ruleDefinitions): ArrayValidator
    {
        return new ArrayValidator($this->getConstraint($ruleDefinitions), $this->validator);
    }

    /**
     * @param Collection|array<string, string|Constraint|array<string|Constraint>> $ruleDefinitions
     * @throws RequestValidationException
     * @throws Utility\InvalidArrayPathException
     */
    public function createTraversableDataValidator($ruleDefinitions): TraversableDataValidator
    {
        $constraint = new TraversableConstraint($this->getConstraint($ruleDefinitions));

        return new TraversableDataValidator($constraint, $this->validator);
    }

    /**
     * @param Collection|array<string, string|Constraint|array<string|Constraint>> $ruleDefinitions
     * @throws RequestValidationException
     * @throws Utility\InvalidArrayPathException
     */
    private function getConstraint($ruleDefinitions): Constraint
    {
        if ($ruleDefinitions instanceof Constraint) {
            return $ruleDefinitions;
        }

        $ruleList      = $this->factory->createRuleListMapBuilder()->build($ruleDefinitions);
        $constraintMap = $this->factory->createConstraintMapBuilder()->build($ruleList);
        return $this->factory->createConstraintCollectionBuilder()->build($constraintMap);
    }
}
