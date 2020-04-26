<?php

declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation\Tests\Unit\Constraint;

use PHPUnit\Framework\TestCase;
use PrinsFrank\SymfonyRequestValidation\Constraint\ConstraintSet;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * @coversDefaultClass \PrinsFrank\SymfonyRequestValidation\Constraint\ConstraintSet
 */
class ConstraintSetTest extends TestCase
{
    /**
     * @covers ::setQueryConstraints
     * @covers ::getQueryConstraints
     * @covers ::getRequestConstraints
     * @covers ::setRequestConstraints
     */
    public function testSetQueryConstraints(): void
    {
        $set = new ConstraintSet();
        static::assertNull($set->getQueryConstraints());
        static::assertNull($set->getRequestConstraints());

        $collectionA = $this->createMock(Collection::class);
        $collectionB = $this->createMock(Collection::class);
        static::assertSame($collectionA, $set->setQueryConstraints($collectionA)->getQueryConstraints());
        static::assertSame($collectionB, $set->setRequestConstraints($collectionB)->getRequestConstraints());
    }
}
