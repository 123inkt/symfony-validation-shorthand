<?php
declare(strict_types=1);

namespace Constraint\Type;

use DigitalRevolution\SymfonyRequestValidation\Constraint\Type\Boolean;
use DigitalRevolution\SymfonyRequestValidation\Constraint\Type\BooleanValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Constraint\Type\BooleanValidator
 */
class BooleanValidatorTest extends TestCase
{
    /** @var ExecutionContext */
    private $context;

    /** @var BooleanValidator */
    private $validator;

    /** @var Boolean */
    private $constraint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->constraint = new Boolean();
        $this->validator  = new BooleanValidator();
        $this->context    = new ExecutionContext(Validation::createValidator(), 'root', $this->createMock(TranslatorInterface::class));
        $this->context->setConstraint($this->constraint);
        $this->validator->initialize($this->context);
    }

    /**
     * @covers ::validate
     */
    public function testValidateUnexpectedTypeException(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $validator = new BooleanValidator();
        $validator->validate(null, new NotBlank());
    }

    /**
     * @dataProvider dataProvider
     * @covers ::validate
     * @param null|bool|int|string $value
     */
    public function testValidateViolations($value): void
    {
        $this->validator->validate($value, $this->constraint);
        static::assertCount(0, $this->context->getViolations());
    }

    /**
     * @return array<string, null|bool|int|string>
     */
    public function dataProvider(): array
    {
        return [
            'null'       => [null],
            '1'          => ['1'],
            '0'          => ['0'],
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
