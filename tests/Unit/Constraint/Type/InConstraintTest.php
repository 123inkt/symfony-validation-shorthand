<?php
declare(strict_types=1);

namespace Constraint\Type;

use DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\InConstraint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InConstraint::class)]
class InConstraintTest extends TestCase
{
    public function testGetRequiredOptions(): void
    {
        $constraint = new InConstraint(['foobar']);
        static::assertSame(['foobar'], $constraint->values);
    }
}
