<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Unit\Constraint\Type;

use DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\IntegerNumber;
use DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\IntegerNumberValidator;
use DigitalRevolution\SymfonyValidationShorthand\Tests\Mock\MockFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validation;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\IntegerNumberValidator
 */
class IntegerNumberValidatorTest extends TestCase
{
    /** @var ExecutionContext */
    private $context;

    /** @var IntegerNumberValidator */
    private $validator;

    /** @var IntegerNumber */
    private $constraint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->constraint = new IntegerNumber();
        $this->validator  = new IntegerNumberValidator();
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
     * @dataProvider dataProvider
     * @covers ::validate
     * @param null|bool|int|string $value
     */
    public function testValidateViolations($value, int $violationCount): void
    {
        $this->validator->validate($value, $this->constraint);
        static::assertCount($violationCount, $this->context->getViolations());
    }

    /**
     * @return array<string, array<mixed, int>>
     */
    public function dataProvider(): array
    {
        return [
            // success
            'null'      => [null, 0],
            '1'         => ['1', 0],
            '0'         => ['0', 0],
            '-1'        => ['-1', 0],
            'int 1'     => [1, 0],
            'int 0'     => [0, 0],
            'float 5.0' => [5.0, 0],
            // failures
            ''          => ['', 1],
            'a'         => ['a', 1],
            '0 prefix'  => ['01', 1],
            'true'      => ['true', 1],
            'float 5.5' => [5.5, 1],
            'bool true' => [true, 1],
            '-'         => ['-', 1],
            'max int'   => [PHP_INT_MAX . '2', 1]
        ];
    }

    /**
     * @covers ::validate
     */
    public function testValidateViolation(): void
    {
        $this->validator->validate('a', $this->constraint);
        $violations = $this->context->getViolations();
        static::assertCount(1, $violations);

        $violation = $violations->get(0);
        static::assertSame($this->constraint->message, $violation->getMessageTemplate());
        static::assertSame(['{{ value }}' => '"a"'], $violation->getParameters());
    }
}
