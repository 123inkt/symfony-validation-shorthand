<?php
declare(strict_types=1);

namespace Constraint;

use DigitalRevolution\SymfonyValidationShorthand\Constraint\ConstraintMapItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

#[CoversClass(ConstraintMapItem::class)]
class ConstraintMapItemTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $constraints = [new NotBlank()];
        $required    = true;

        // without required
        $item = new ConstraintMapItem($constraints);
        static::assertSame($constraints, $item->getConstraints());
        static::assertFalse($item->isRequired());

        // with required
        $item = new ConstraintMapItem($constraints, $required);
        static::assertSame($constraints, $item->getConstraints());
        static::assertTrue($item->isRequired());
    }
}
