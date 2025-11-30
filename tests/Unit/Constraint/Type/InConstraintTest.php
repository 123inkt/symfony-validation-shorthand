<?php
declare(strict_types=1);

namespace Constraint\Type;

use DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\InConstraint;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\InConstraint
 */
class InConstraintTest extends TestCase
{
    /**
     * @covers ::getRequiredOptions
     */
    public function testGetRequiredOptions(): void
    {
        $constraint = new InConstraint(['foobar']);
        static::assertSame(['foobar'], $constraint->values);
    }
}
