<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Unit;

use DigitalRevolution\SymfonyValidationShorthand\ConstraintFactory;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyValidationShorthand\ConstraintFactory
 * @covers ::__construct
 */
class ConstraintFactoryTest extends TestCase
{
    /**
     * @covers ::fromRuleDefinitions
     * @throws Exception
     */
    public function testFromRuleDefinitionsConstraintOnly(): void
    {
        $factory = new ConstraintFactory();
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
        $expect  = new Assert\Collection(['email' => new Assert\Required([new Assert\Email(), new Assert\NotNull()])]);
        static::assertEquals($expect, $factory->fromRuleDefinitions(['email' => 'required|email'], false));
    }

    /**
     * @covers ::fromRuleDefinitions
     * @throws Exception
     */
    public function testFromRuleDefinitionsWithRuleAllowExtraFields(): void
    {
        $factory = new ConstraintFactory();
        $expect  = new Assert\Collection(
            ['fields' => ['email' => new Assert\Required([new Assert\Email(), new Assert\NotNull()])], 'allowExtraFields' => true]
        );
        static::assertEquals($expect, $factory->fromRuleDefinitions(['email' => 'required|email'], true));
    }
}
