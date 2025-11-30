<?php
declare(strict_types=1);

namespace Constraint\Type;

use DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\InConstraint;
use DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\InConstraintValidator;
use DigitalRevolution\SymfonyValidationShorthand\Tests\Mock\MockFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validation;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\InConstraintValidator
 */
class InConstraintValidatorTest extends TestCase
{
    private ExecutionContext $context;
    private InConstraintValidator $validator;
    private InConstraint $constraint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->constraint = new InConstraint(['foobar']);
        $this->validator  = new InConstraintValidator();
        $this->context    = new ExecutionContext(Validation::createValidator(), 'root', MockFactory::createTranslator($this));
        $this->context->setConstraint($this->constraint);
        $this->validator->initialize($this->context);
    }

    /**
     * @covers ::validate
     */
    public function testValidateUnexpectedTypeException(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new NotBlank());
    }

    /**
     * @covers ::validate
     */
    public function testValidateShouldSkipNullValue(): void
    {
        $this->validator->validate(null, $this->constraint);
        static::assertCount(0, $this->context->getViolations());
    }

    /**
     * @covers ::validate
     */
    public function testValidateShouldPassOnAllowedValue(): void
    {
        $this->validator->validate('foobar', $this->constraint);
        static::assertCount(0, $this->context->getViolations());
    }

    /**
     * @covers ::validate
     */
    public function testValidateShouldPassOnNumericValue(): void
    {
        $constraint = new InConstraint(['2', '3', '4', '5']);
        $this->validator->validate(5, $constraint);
        static::assertCount(0, $this->context->getViolations());
    }

    /**
     * @covers ::validate
     */
    public function testValidateShouldFailOnDisallowedValue(): void
    {
        $this->validator->validate('invalid', $this->constraint);
        $violations = $this->context->getViolations();
        static::assertCount(1, $violations);

        $expected = ['{{ value }}' => '"invalid"', '{{ values }}' => 'foobar'];
        static::assertSame($expected, $violations->get(0)->getParameters());
    }
}
