<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Constraint;

use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintHelper
 */
class ConstraintHelperTest extends TestCase
{
    /**
     * @covers ::createAllConstraint
     */
    public function testCreateAllConstraint(): void
    {
        // empty rules should not resolve to Assert\All
        static::assertNull(ConstraintHelper::createAllConstraint([]));

        // multiple constraints don't resolve to Assert\All either
        static::assertNull(ConstraintHelper::createAllConstraint(['fieldA' => new Assert\NotBlank(), 'fieldB' => new Assert\NotNull()]));

        // only '*' key should resolve to Assert\All
        static::assertNull(ConstraintHelper::createAllConstraint(['+' => new Assert\NotBlank()]));

        $expected = new Assert\All(new Assert\NotBlank());
        static::assertEquals($expected, ConstraintHelper::createAllConstraint(['*' => new Assert\NotBlank()]));
    }
}
