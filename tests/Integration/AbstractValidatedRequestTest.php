<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Integration;

use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use DigitalRevolution\SymfonyRequestValidation\Tests\Mock\MockValidatedRequest;
use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;

/**
 * @coversNothing
 */
class AbstractValidatedRequestTest extends TestCase
{
//    public function testDummy(): void
//    {
//        $data       = ['name' => null];
//        $validator  = Validation::createValidator();
//        $constraint = new Collection(['fields' => ['name' => new Type(['string'])]]);
//
//        $violations = $validator->validate($data, $constraint);
//        static::assertCount(0, $violations);
//    }

    /**
     * @dataProvider dataProvider
     * @param array<string, mixed> $data
     * @param Collection|array<string, string|Constraint|array<string|Constraint>>|null $rules
     * @throws RequestValidationException
     */
    public function testGetRequestValidation(array $data, $rules, bool $isValid): void
    {
        $request = new Request($data);
        $stack   = new RequestStack();
        $stack->push($request);

        $validator       = Validation::createValidator();
        $validationRules = new ValidationRules();
        $validationRules->setQueryRules($rules);

        // expect exception
        if ($isValid === false) {
            $this->expectException(RequestValidationException::class);
            new MockValidatedRequest($stack, $validator, $validationRules);
        }

        // expect success
        $request = new MockValidatedRequest($stack, $validator, $validationRules);
        static::assertTrue($request->isValid());
    }

    public function dataProvider(): Generator
    {
        // test required fields
        yield "required: name exists" => [['name' => 'Frank'], ['name' => 'required'], true];
        yield "required: name is empty" => [['name' => ''], ['name' => 'required'], true];
        yield "required: name can't be null" => [['name' => null], ['name' => 'required'], false];
        yield "required: name is nullable" => [['name' => null], ['name' => 'required|nullable'], true];
        yield "required: name field is missing" => [[], ['name' => 'required'], false];

        // test optional string
        yield "optional: name exists" => [['name' => 'Frank'], ['name' => 'string'], true];
        yield "optional: name is empty" => [['name' => ''], ['name' => 'string'], true];
        yield "optional: name field is missing" => [[], ['name' => 'string'], true];
        yield "optional: name can't be null" => [['name' => null], ['name' => 'string'], false];
        yield "optional: name is nullable" => [['name' => null], ['name' => 'string|nullable'], true];
    }
}
