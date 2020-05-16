<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Unit;

use DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\RequestConstraint;
use DigitalRevolution\SymfonyValidationShorthand\RequestValidationException;
use DigitalRevolution\SymfonyValidationShorthand\Tests\Mock\MockValidatedRequest;
use DigitalRevolution\SymfonyValidationShorthand\RequestValidationRules;
use DigitalRevolution\SymfonyValidationShorthand\Utility\InvalidArrayPathException;
use Exception;
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
 * @coversDefaultClass \DigitalRevolution\SymfonyValidationShorthand\AbstractValidatedRequest
 */
class AbstractValidatedRequestTest extends TestCase
{
    /**
     * @covers ::__construct
     * @throws Exception
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
     * @throws Exception
     */
    public function testConstructorWithoutViolations(): void
    {
        $request = new Request();
        $stack   = new RequestStack();
        $stack->push($request);

        $rules = new RequestValidationRules([]);

        $validatedRequest = new MockValidatedRequest($stack, Validation::createValidator(), $rules);
        static::assertTrue($validatedRequest->isValid());
        static::assertSame($request, $validatedRequest->getRequest());
    }

    /**
     * @covers ::__construct
     * @covers ::validate
     * @covers ::handleViolations
     * @throws RequestValidationException|InvalidArrayPathException
     */
    public function testConstructorWithViolations(): void
    {
        $request = new Request();
        $stack   = new RequestStack();
        $stack->push($request);

        // create rules
        $constraint = new Collection(['fields' => ['test' => new NotBlank()]]);
        $rules      = new RequestValidationRules(['request' => $constraint]);

        // create violations
        $violations = new ConstraintViolationList();
        $violations->add($this->createMock(ConstraintViolation::class));

        // create validator
        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects(self::once())
            ->method('validate')
            ->with(...[$request, new RequestConstraint(['request' => $constraint])])
            ->willReturn($violations);

        $this->expectException(RequestValidationException::class);
        new MockValidatedRequest($stack, $validator, $rules);
    }
}
