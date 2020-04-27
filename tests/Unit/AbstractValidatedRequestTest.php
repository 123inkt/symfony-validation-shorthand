<?php
declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation\Tests\Unit;

use PrinsFrank\SymfonyRequestValidation\AbstractValidatedRequest;
use PHPUnit\Framework\TestCase;
use PrinsFrank\SymfonyRequestValidation\RequestValidationException;
use PrinsFrank\SymfonyRequestValidation\Tests\Mock\MockValidatedRequest;
use PrinsFrank\SymfonyRequestValidation\ValidationRules;
use PrinsFrank\SymfonyRequestValidation\Validator\RequestValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @coversDefaultClass \PrinsFrank\SymfonyRequestValidation\AbstractValidatedRequest
 */
class AbstractValidatedRequestTest extends TestCase
{
    /**
     * @covers ::__construct
     * @throws RequestValidationException
     */
    public function testConstructorNullRequest(): void
    {
        $stack = new RequestStack();

        $validatedRequest = new MockValidatedRequest($stack, Validation::createValidator());
        static::assertFalse($validatedRequest->isValidated());
    }

    /**
     * @covers ::__construct
     * @covers ::validate
     * @throws RequestValidationException
     */
    public function testConstructorWithoutViolations(): void
    {
        $request = new Request();
        $stack   = new RequestStack();
        $stack->push($request);

        $rules = new ValidationRules();

        $validatedRequest = new MockValidatedRequest($stack, Validation::createValidator(), $rules);
        static::assertTrue($validatedRequest->isValidated());
    }

    /**
     * @covers ::__construct
     * @covers ::validate
     * @covers ::handleViolations
     * @throws RequestValidationException
     */
    public function testConstructorWithViolations(): void
    {
        $request = new Request();
        $stack   = new RequestStack();
        $stack->push($request);

        // create rules
        $constraint = new Collection(['fields' => ['test' => new NotBlank()]]);
        $rules      = new ValidationRules();
        $rules->setRequestRules($constraint);

        // create violations
        $violations = new ConstraintViolationList();
        $violations->add($this->createMock(ConstraintViolation::class));

        // create validator
        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects(self::once())
            ->method('validate')
            ->with([], $constraint)
            ->willReturn($violations);

        $this->expectException(RequestValidationException::class);
        new MockValidatedRequest($stack, $validator, $rules);
    }
}
