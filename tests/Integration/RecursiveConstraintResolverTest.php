<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Integration;

use DigitalRevolution\SymfonyRequestValidation\Constraint\RecursiveConstraintResolver;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class RecursiveConstraintResolverTest extends TestCase
{
    /**
     *
     * @throws RequestValidationException
     */
    public function testResolve(): void
    {
        $data  = ['first_name' => ''];
        $rules = ['first_name' => 'filled|nullable'];

        $resolver   = new RecursiveConstraintResolver();
        $constraint = $resolver->resolve($rules);

        $validator  = Validation::createValidator();
        $violations = $validator->validate($data, $constraint);
        static::assertCount(0, $violations);
    }

    /**
     *
     * @throws RequestValidationException
     */
    public function testResolveNestedArray(): void
    {
        $data  = [];
        $rules = ['person.*.first_name' => 'filled|min:5'];

        $resolver   = new RecursiveConstraintResolver();
        $constraint = $resolver->resolve($rules);

        $validator  = Validation::createValidator();
        $violations = $validator->validate($data, $constraint);
        static::assertCount(0, $violations);
    }

    /**
     *
     * @throws RequestValidationException
     */
    public function testResolveKeylessData(): void
    {
        $data  = [
            [1, 'ab'],
            [2, 'cd']
        ];
        $rules = [
            '*.#0' => 'integer',
            "*.#1" => 'string|min:1'
        ];

        $resolver   = new RecursiveConstraintResolver();
        $constraint = $resolver->resolve($rules);

        $validator  = Validation::createValidator();
        $violations = $validator->validate($data, $constraint);
        static::assertCount(0, $violations);
    }
}
