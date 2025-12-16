<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Unit;

use DigitalRevolution\SymfonyValidationShorthand\ConstraintFactory;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;

#[CoversClass(ConstraintFactory::class)]
class ConstraintFactoryTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testFromRuleDefinitionsConstraintOnly(): void
    {
        $factory = new ConstraintFactory();
        $constraint = new Assert\NotBlank();
        static::assertSame($constraint, $factory->fromRuleDefinitions($constraint));
    }

    /**
     * @throws Exception
     */
    public function testFromRuleDefinitionsConstraintListOnly(): void
    {
        $factory = new ConstraintFactory();
        $constraintA = new Assert\NotBlank();
        $constraintB = new Assert\NotNull();
        static::assertSame([$constraintA, $constraintB], $factory->fromRuleDefinitions([$constraintA, $constraintB]));
    }

    /**
     * @throws Exception
     */
    public function testFromRuleDefinitionsWithRule(): void
    {
        $factory = new ConstraintFactory();
        $expect = new Assert\Collection(['email' => new Assert\Required([new Assert\Email(), new Assert\NotNull()])]);
        static::assertEquals($expect, $factory->fromRuleDefinitions(['email' => 'required|email'], false));
    }

    /**
     * @throws Exception
     */
    public function testFromRuleDefinitionsWithRuleAllowExtraFields(): void
    {
        $factory = new ConstraintFactory();
        $expect = new Assert\Collection(
            ['email' => new Assert\Required([new Assert\Email(), new Assert\NotNull()])],
            allowExtraFields: true
        );
        static::assertEquals($expect, $factory->fromRuleDefinitions(['email' => 'required|email'], true));
    }
}
