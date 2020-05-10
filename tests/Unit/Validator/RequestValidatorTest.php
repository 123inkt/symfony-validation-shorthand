<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Validator;

use DigitalRevolution\SymfonyRequestValidation\Validator\RequestValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Validator\RequestValidator
 * @covers \DigitalRevolution\SymfonyRequestValidation\Validator\AbstractValidator
 */
class RequestValidatorTest extends TestCase
{
    /**
     * @covers ::validate
     */
    public function testValidate(): void
    {
        $constraint    = new NotBlank();
        $violationList = new ConstraintViolationList();
        $request       = new Request();

        /** @var ValidatorInterface|MockObject $validatorMock */
        $validatorMock = $this->createMock(ValidatorInterface::class);
        $validatorMock
            ->expects(self::once())
            ->method('validate')
            ->with($request, $constraint)
            ->willReturn($violationList);

        $requestValidator = new RequestValidator($constraint, $validatorMock);
        static::assertSame($violationList, $requestValidator->validate($request));
    }
}
