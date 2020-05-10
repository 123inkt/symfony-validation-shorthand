<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Parser;

use DigitalRevolution\SymfonyRequestValidation\Parser\RuleList;
use DigitalRevolution\SymfonyRequestValidation\Parser\RuleListMap;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Parser\RuleListMap
 */
class RuleListMapTest extends TestCase
{
    /**
     * @covers ::set
     * @covers ::getIterator
     * @throws Exception
     */
    public function testSetAndIterator(): void
    {
        $list    = new RuleList();
        $listMap = new RuleListMap();
        static::assertSame([], iterator_to_array($listMap->getIterator()));

        $listMap->set('test', $list);
        static::assertSame(['test' => $list], iterator_to_array($listMap->getIterator()));
    }
}
