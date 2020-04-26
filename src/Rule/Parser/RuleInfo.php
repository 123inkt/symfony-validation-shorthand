<?php

declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation\Rule\Parser;

class RuleInfo
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
