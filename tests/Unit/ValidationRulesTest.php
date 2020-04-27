<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit;

use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\ValidationRules
 */
class ValidationRulesTest extends TestCase
{
    /**
     * @covers ::setQueryRules
     * @covers ::getQueryRules
     * @covers ::setRequestRules
     * @covers ::getRequestRules
     */
    public function testSetRequestRules(): void
    {
        $rules = new ValidationRules();
        static::assertNull($rules->getQueryRules());
        static::assertNull($rules->getRequestRules());

        $collectionA = $this->createMock(Collection::class);
        $collectionB = $this->createMock(Collection::class);
        static::assertSame($collectionA, $rules->setQueryRules($collectionA)->getQueryRules());
        static::assertSame($collectionB, $rules->setRequestRules($collectionB)->getRequestRules());
    }
}
