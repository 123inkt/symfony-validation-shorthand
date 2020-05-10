<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Validator;

use DigitalRevolution\SymfonyRequestValidation\Validator\DataValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Validator\DataValidator
 * @covers \DigitalRevolution\SymfonyRequestValidation\Validator\AbstractValidator
 */
class DataValidatorTest extends TestCase
{
    /**
     * @covers ::validate
     */
    public function testValidate(): void
    {
        $constraint    = new NotBlank();
        $violationList = new ConstraintViolationList();
        $data          = [];

        /** @var ValidatorInterface|MockObject $validatorMock */
        $validatorMock = $this->createMock(ValidatorInterface::class);
        $validatorMock
            ->expects(self::once())
            ->method('validate')
            ->with($data, $constraint)
            ->willReturn($violationList);

        $dataValidator = new DataValidator($constraint, $validatorMock);
        static::assertSame($violationList, $dataValidator->validate($data));
    }
}
