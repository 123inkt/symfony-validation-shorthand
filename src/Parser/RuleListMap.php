<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Parser;

use ArrayIterator;
use IteratorAggregate;

/**
 * A string key and RuleList value map
 */
class RuleListMap implements IteratorAggregate
{
    /** @var array<string, RuleList> */
    private $map = [];

    public function set(string $key, RuleList $ruleSet): self
    {
        $this->map[$key] = $ruleSet;

        return $this;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->map);
    }
}
