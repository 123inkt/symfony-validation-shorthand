<?php

declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation\Rule\Parser;

use InvalidArgumentException;

class Rule
{
    /** @var string */
    private $name;

    /** @var string[] */
    private $arguments;

    public function __construct(string $name, array $arguments = [])
    {
        $this->name = $name;
        $this->arguments = $arguments;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIntArgument(int $index): int
    {
        if (isset($this->arguments[$index]) === null) {
            throw new InvalidArgumentException('Missing `int` argument');
        }

        $argument = (int)$this->arguments[$index];
        if ((string)$argument !== $this->arguments[$index]) {
            throw new InvalidArgumentException('Argument is not an int: ' . $this->arguments[$index]);
        }

        return $argument;
    }

    /**
     * @return string[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param string[] $arguments
     */
    public function setArguments(array $arguments): self
    {
        $this->arguments = $arguments;
        return $this;
    }
}
