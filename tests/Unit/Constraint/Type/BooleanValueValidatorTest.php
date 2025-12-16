<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Unit\Constraint\Type;

use DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\BooleanValue;
use DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\BooleanValueValidator;
use DigitalRevolution\SymfonyValidationShorthand\Tests\Mock\MockFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validation;

#[CoversClass(BooleanValueValidator::class)]
class BooleanValueValidatorTest extends TestCase
{
    private ExecutionContext $context;
    private BooleanValueValidator $validator;
    private BooleanValue $constraint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->constraint = new BooleanValue();
        $this->validator  = new BooleanValueValidator();
        $this->context    = new ExecutionContext(Validation::createValidator(), 'root', MockFactory::createTranslator($this));
        $this->context->setConstraint($this->constraint);
        $this->validator->initialize($this->context);
    }

    public function testValidateUnexpectedTypeException(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $validator = new BooleanValueValidator();
        $validator->validate(null, new NotBlank());
    }

    /**
     * @param null|bool|int|string $value
     */
    #[DataProvider('dataProvider')]
    public function testValidateViolations($value): void
    {
        $this->validator->validate($value, $this->constraint);
        static::assertCount(0, $this->context->getViolations());
    }

    /**
     * @return array<string, array<null|bool|int|string>>
     */
    public static function dataProvider(): array
    {
        return [
            'null'       => [null],
            'string 1'          => ['1'],
            'string 0'          => ['0'],
            'on'         => ['on'],
            'off'        => ['off'],
            'true'       => ['true'],
            'false'      => ['false'],
            'int 1'      => [1],
            'int 0'      => [0],
            'bool true'  => [true],
            'bool false' => [false]
        ];
    }

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
