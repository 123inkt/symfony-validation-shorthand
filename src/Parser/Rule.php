<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Parser;

use InvalidArgumentException;

class Rule
{
    /** @var string */
    private $name;

    /** @var string[] */
    private $parameters;

    public function __construct(string $name, array $parameters = [])
    {
        $this->name       = $name;
        $this->parameters = $parameters;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParameter(int $offset): string
    {
        if (isset($this->parameters[$offset]) === false) {
            throw new InvalidArgumentException('Missing int argument for rule: ' . $this->getName());
        }

        return $this->parameters[$offset];
    }

    public function getIntParam(int $offset): int
    {
        $argument    = $this->getParameter($offset);
        $intArgument = (int)$argument;
        if ((string)$intArgument !== $argument) {
            throw new InvalidArgumentException(
                'Invalid int argument for rule: ' . $this->getName() . ', value: ' . $this->parameters[$offset]
            );
        }

        return $intArgument;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
