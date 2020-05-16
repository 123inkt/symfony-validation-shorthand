<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Constraint;

use ArrayIterator;
use IteratorAggregate;

/**
 * @template-implements IteratorAggregate<string, ConstraintMapItem>
 */
class ConstraintMap implements IteratorAggregate
{
    /** @var array<string, ConstraintMapItem> */
    private $map = [];

    public function set(string $key, ConstraintMapItem $item): self
    {
        $this->map[$key] = $item;

        return $this;
    }

    /**
     * @return ArrayIterator<string, ConstraintMapItem>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->map);
    }
}
