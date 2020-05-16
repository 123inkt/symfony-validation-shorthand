<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Integration;

use DigitalRevolution\SymfonyValidationShorthand\ConstraintFactory;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class IntegrationTest extends TestCase
{
    /** @var ConstraintFactory */
    protected $constraintFactory;

    /** @var ValidatorInterface */
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->constraintFactory = new ConstraintFactory();
        $this->validator         = Validation::createValidator();
    }

    /**
     * @param mixed                                                                $data
     * @param Constraint|array<string, string|Constraint|array<string|Constraint>> $ruleDefinitions
     * @throws Exception
     */
    protected static function assertHasViolations($data, $ruleDefinitions): void
    {
        static::assertCountViolations(1, $data, $ruleDefinitions);
    }

    /**
     * @param mixed                                                                $data
     * @param Constraint|array<string, string|Constraint|array<string|Constraint>> $ruleDefinitions
     * @throws Exception
     */
    protected static function assertHasNoViolations($data, $ruleDefinitions): void
    {
        static::assertCountViolations(0, $data, $ruleDefinitions);
    }

    /**
     * @param mixed                                                                $data
     * @param Constraint|array<string, string|Constraint|array<string|Constraint>> $ruleDefinitions
     * @throws Exception
     */
    protected static function assertCountViolations(int $expectedCount, $data, $ruleDefinitions): void
    {
        $constraint = (new ConstraintFactory())->fromRuleDefinitions($ruleDefinitions);
        $validator  = Validation::createValidator();
        $violations = $validator->validate($data, $constraint);

        static::assertCount($expectedCount, $violations);
    }
}
