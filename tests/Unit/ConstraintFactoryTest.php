<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit;

use DigitalRevolution\SymfonyRequestValidation\Constraint\Type\RequestConstraint;
use DigitalRevolution\SymfonyRequestValidation\ConstraintFactory;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationRules;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\ConstraintFactory
 * @covers ::__construct
 */
class ConstraintFactoryTest extends TestCase
{
    /**
     * @covers ::createRequestConstraint
     * @throws Exception
     */
    public function testCreateRequestConstraint(): void
    {
        $factory = new ConstraintFactory();

        // without any rules
        $result = $factory->createRequestConstraint(new RequestValidationRules([]));
        static::assertEquals(new RequestConstraint(), $result);

        $constraintA = new Assert\NotNull();
        $constraintB = new Assert\NotBlank();
        $result      = $factory->createRequestConstraint(new RequestValidationRules(['query' => $constraintA, 'request' => $constraintB]));
        static::assertEquals(new RequestConstraint(['query' => $constraintA, 'request' => $constraintB]), $result);
    }

    /**
     * @covers ::fromRuleDefinitions
     * @throws Exception
     */
    public function testFromRuleDefinitionsConstraintOnly(): void
    {
        $factory    = new ConstraintFactory();
        $constraint = new Assert\NotBlank();
        static::assertSame($constraint, $factory->fromRuleDefinitions($constraint));
    }

    /**
     * @covers ::fromRuleDefinitions
     * @throws Exception
     */
    public function testFromRuleDefinitionsWithRule(): void
    {
        $factory = new ConstraintFactory();
        $expect  = new Assert\Collection([
            'email' => new Assert\Required([
                new Assert\Email(),
                new Assert\NotNull()
            ])
        ]);
        static::assertEquals($expect, $factory->fromRuleDefinitions(['email' => 'required|email']));
    }
}
