<?php
declare(strict_types=1);

use DigitalRevolution\SymfonyRequestValidation\Constraint\Type\RequestConstraint;
use DigitalRevolution\SymfonyRequestValidation\ConstraintFactory;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationRules;
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
     * @covers ::createConstraintFromDefinitions
     * @throws Exception
     */
    public function testCreateConstraintFromDefinitionsConstraintOnly(): void
    {
        $factory    = new ConstraintFactory();
        $constraint = new Assert\NotBlank();
        static::assertSame($constraint, $factory->createConstraintFromDefinitions($constraint));
    }

    /**
     * @covers ::createConstraintFromDefinitions
     * @throws Exception
     */
    public function testCreateConstraintFromDefinitionsWithRule(): void
    {
        $factory = new ConstraintFactory();
        $expect  = new Assert\Collection([
            'email' => new Assert\Required([
                new Assert\Email(),
                new Assert\NotNull()
            ])
        ]);
        static::assertEquals($expect, $factory->createConstraintFromDefinitions(['email' => 'required|email']));
    }
}
