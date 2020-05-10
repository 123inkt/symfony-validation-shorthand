<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Validator;

use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintResolver;
use DigitalRevolution\SymfonyRequestValidation\Parser\ValidationRuleParser;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationRules;
use DigitalRevolution\SymfonyRequestValidation\Validator\RequestValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Negative;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Validator\RequestValidator
 * @covers ::__construct
 */
class RequestValidatorTest extends TestCase
{
    /** @var ValidatorInterface */
    private $validator;

    /** @var RequestValidator */
    private $requestValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator        = Validation::createValidator();
        $this->requestValidator = new RequestValidator($this->validator, new ValidationRuleParser(new ConstraintResolver()));
    }

    /**
     * @covers ::validate
     * @throws RequestValidationException
     */
    public function testValidateNoRules(): void
    {
        static::assertCount(0, $this->requestValidator->validate(new Request(), new RequestValidationRules()));
    }

    /**
     * @covers ::validate
     * @throws RequestValidationException
     */
    public function testValidateRulesWithoutViolation(): void
    {
        $request           = new Request(['test' => 'unit'], ['foo' => 'bar']);
        $queryConstraint   = new Collection(['fields' => ['test' => new NotBlank()]]);
        $requestConstraint = new Collection(['fields' => ['foo' => new NotBlank()]]);
        $rules             = new RequestValidationRules();
        $rules->setQueryRules($queryConstraint);
        $rules->setRequestRules($requestConstraint);

        static::assertCount(0, $this->requestValidator->validate($request, $rules));
    }

    /**
     * @covers ::validate
     * @throws RequestValidationException
     */
    public function testValidateRulesWithViolation(): void
    {
        $request           = new Request(['test' => 'unit'], ['foo' => 'bar']);
        $queryConstraint   = new Collection(['fields' => ['test' => new Positive()]]);
        $requestConstraint = new Collection(['fields' => ['foo' => new Negative()]]);
        $rules             = new RequestValidationRules();
        $rules->setQueryRules($queryConstraint);
        $rules->setRequestRules($requestConstraint);

        $violations = $this->requestValidator->validate($request, $rules);
        static::assertCount(2, $violations);

        /** @var ConstraintViolation $violationA */
        /** @var ConstraintViolation $violationB */
        [$violationA, $violationB] = $violations;
        static::assertInstanceOf(Positive::class, $violationA->getConstraint());
        static::assertInstanceOf(Negative::class, $violationB->getConstraint());
    }
}
