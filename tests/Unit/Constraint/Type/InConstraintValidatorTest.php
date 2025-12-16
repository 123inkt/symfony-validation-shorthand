<?php
declare(strict_types=1);

namespace Constraint\Type;

use DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\InConstraint;
use DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\InConstraintValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

#[CoversClass(InConstraintValidator::class)]
class InConstraintValidatorTest extends TestCase
{
    private ExecutionContext $context;
    private InConstraintValidator $validator;
    private InConstraint $constraint;

    protected function setUp(): void
    {
        parent::setUp();
        $translatorStub = $this->createStub(TranslatorInterface::class);
        $translatorStub->method('trans')->willReturn('unit test');

        $this->constraint = new InConstraint(['values' => ['foobar']]);
        $this->validator  = new InConstraintValidator();
        $this->context  = new ExecutionContext(Validation::createValidator(), 'root', $translatorStub);
        $this->context->setConstraint($this->constraint);
        $this->validator->initialize($this->context);
    }

    public function testValidateUnexpectedTypeException(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new NotBlank());
    }

    public function testValidateShouldSkipNullValue(): void
    {
        $this->validator->validate(null, $this->constraint);
        static::assertCount(0, $this->context->getViolations());
    }

    public function testValidateShouldPassOnAllowedValue(): void
    {
        $this->validator->validate('foobar', $this->constraint);
        static::assertCount(0, $this->context->getViolations());
    }

    public function testValidateShouldPassOnNumericValue(): void
    {
        $constraint = new InConstraint(['values' => ['2', '3', '4', '5']]);
        $this->validator->validate(5, $constraint);
        static::assertCount(0, $this->context->getViolations());
    }

    public function testValidateShouldFailOnDisallowedValue(): void
    {
        $this->validator->validate('invalid', $this->constraint);
        $violations = $this->context->getViolations();
        static::assertCount(1, $violations);

        $expected = ['{{ value }}' => '"invalid"', '{{ values }}' => 'foobar'];
        static::assertSame($expected, $violations->get(0)->getParameters());
    }
}
