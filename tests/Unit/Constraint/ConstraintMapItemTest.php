<?php
declare(strict_types=1);

namespace Constraint;

use DigitalRevolution\SymfonyValidationShorthand\Constraint\ConstraintMapItem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyValidationShorthand\Constraint\ConstraintMapItem
 */
class ConstraintMapItemTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::isRequired
     * @covers ::getConstraints
     */
    public function testConstructorAndGetters(): void
    {
        $constraints = [new NotBlank()];

        // without required
        $item = new ConstraintMapItem($constraints);
        static::assertSame($constraints, $item->getConstraints());
        static::assertFalse($item->isRequired());

        // with required
        $item = new ConstraintMapItem($constraints, true);
        static::assertSame($constraints, $item->getConstraints());
        static::assertTrue($item->isRequired());
    }
}
