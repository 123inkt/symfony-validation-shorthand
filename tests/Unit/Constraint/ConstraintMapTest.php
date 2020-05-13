<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Constraint;

use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintMap;
use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintMapItem;
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
    public function testSetAndIterator(): void
    {
        $constraintMap = new ConstraintMap();
        $mapItem       = new ConstraintMapItem([new NotBlank()], true);
        static::assertSame([], iterator_to_array($constraintMap->getIterator()));

        $constraintMap->set('test', $mapItem);
        static::assertSame(['test' => $mapItem], iterator_to_array($constraintMap->getIterator()));
    }
}
