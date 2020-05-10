<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Constraint;

use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintMap;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintMap
 */
class ConstraintMapTest extends TestCase
{
    /**
     * @covers ::set
     * @covers ::getIterator
     * @throws Exception
     */
    public function testSet(): void
    {
        $constraint    = new NotBlank();
        $constraintMap = new ConstraintMap();
        static::assertSame([], iterator_to_array($constraintMap->getIterator()));

        $constraintMap->set('test', $constraint);
        static::assertSame(['test' => $constraint], iterator_to_array($constraintMap->getIterator()));
    }
}
