<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit;

use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use DigitalRevolution\SymfonyRequestValidation\Tests\Mock\MockValidatedRequest;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationRules;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\AbstractValidatedRequest
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

        $this->expectException(RequestValidationException::class);
        $this->expectExceptionMessage('Request is missing, unable to validate');
        new MockValidatedRequest($stack, Validation::createValidator());
    }

    /**
     * @covers ::__construct
     * @covers ::validate
     * @covers ::isValid
     * @covers ::getRequest
     * @throws RequestValidationException
     */
    public function testConstructorWithoutViolations(): void
    {
        $request = new Request();
        $stack   = new RequestStack();
        $stack->push($request);

        $rules = new RequestValidationRules();

        $validatedRequest = new MockValidatedRequest($stack, Validation::createValidator(), $rules);
        static::assertTrue($validatedRequest->isValid());
        static::assertSame($request, $validatedRequest->getRequest());
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
        $rules      = new RequestValidationRules();
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
