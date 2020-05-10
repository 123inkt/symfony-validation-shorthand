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

class ValidatorFactory
{
    /** @var MapBuilderFactoryInterface */
    private $factory;

    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator = null, MapBuilderFactoryInterface $factory = null)
    {
        $this->validator = $validator ?? Validation::createValidator();
        $this->factory   = $factory ?? new MapBuilderFactory();
    }

    /**
     * @throws RequestValidationException
     * @throws InvalidArrayPathException
     */
    public function createRequestValidator(RequestValidationRules $validationRules): RequestValidator
    {
        $options            = [
            'queryConstraint'   => null,
            'requestConstraint' => null
        ];
        $queryDefinitions   = $validationRules->getQueryRules();
        $requestDefinitions = $validationRules->getRequestRules();

        if ($queryDefinitions !== null) {
            $options['queryConstraint'] = $this->getConstraint($queryDefinitions);
        }
        if ($requestDefinitions !== null) {
            $options['requestConstraint'] = $this->getConstraint($requestDefinitions);
        }

        return new RequestValidator(new RequestConstraint($options), $this->validator);
    }

    /**
     * @param Assert\Collection|array<string, string|Constraint|array<string|Constraint>> $ruleDefinitions
     * @throws RequestValidationException
     * @throws InvalidArrayPathException
     */
    public function createDataValidator($ruleDefinitions): DataValidator
    {
        return new DataValidator($this->getConstraint($ruleDefinitions), $this->validator);
    }

    /**
     * @param Assert\Collection|array<string, string|Constraint|array<string|Constraint>> $ruleDefinitions
     * @throws RequestValidationException
     * @throws InvalidArrayPathException
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
