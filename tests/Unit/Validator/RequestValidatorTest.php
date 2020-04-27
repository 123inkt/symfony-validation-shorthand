<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Validator;

use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = Validation::createValidator();
    }

    /**
     * @covers ::validate
     */
    public function testValidateNoRules(): void
    {
        $requestValidator = new RequestValidator($this->validator);
        static::assertCount(0, $requestValidator->validate(new Request(), new ValidationRules()));
    }

    /**
     * @covers ::validate
     */
    public function testValidateRulesWithoutViolation(): void
    {
        $request           = new Request(['test' => 'unit'], ['foo' => 'bar']);
        $queryConstraint   = new Collection(['fields' => ['test' => new NotBlank()]]);
        $requestConstraint = new Collection(['fields' => ['foo' => new NotBlank()]]);
        $rules             = new ValidationRules();
        $rules->setQueryRules($queryConstraint);
        $rules->setRequestRules($requestConstraint);

        $requestValidator = new RequestValidator($this->validator);
        static::assertCount(0, $requestValidator->validate($request, $rules));
    }

    /**
     * @covers ::validate
     */
    public function testValidateRulesWithViolation(): void
    {
        $request           = new Request(['test' => 'unit'], ['foo' => 'bar']);
        $queryConstraint   = new Collection(['fields' => ['test' => new Positive()]]);
        $requestConstraint = new Collection(['fields' => ['foo' => new Negative()]]);
        $rules             = new ValidationRules();
        $rules->setQueryRules($queryConstraint);
        $rules->setRequestRules($requestConstraint);

        $requestValidator = new RequestValidator($this->validator);
        $violations       = $requestValidator->validate($request, $rules);
        static::assertCount(2, $violations);

        /** @var ConstraintViolation $violationA */
        /** @var ConstraintViolation $violationB */
        [$violationA, $violationB] = $violations;
        static::assertInstanceOf(Positive::class, $violationA->getConstraint());
        static::assertInstanceOf(Negative::class, $violationB->getConstraint());
    }
}
