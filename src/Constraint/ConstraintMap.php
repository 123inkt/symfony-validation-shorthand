<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use ArrayIterator;
use IteratorAggregate;
use Symfony\Component\Validator\Constraint;

/**
 * @template-implements IteratorAggregate<string, Constraint>
 */
class ConstraintMap implements IteratorAggregate
{
    /** @var array<string, Constraint> */
    private $map = [];

    public function set(string $key, Constraint $constraint): self
    {
        $this->map[$key] = $constraint;

        return $this;
    }

    /**
     * @return ArrayIterator<string, Constraint>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->map);
    }
}
