<?php
declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation;

use Symfony\Component\Validator\Constraint;

class ValidationRules
{
    /** @var array<string, array<string|Constraint>> */
    private $queryRules = [];

    /** @var array<string, array<string|Constraint>> */
    private $requestRules = [];

    /**
     * @return array<string, array<string|Constraint>>
     */
    public function getQueryRules(): array
    {
        return $this->queryRules;
    }

    /**
     * @param array<string, array<string|Constraint>> $queryRules
     */
    public function setQueryRules(array $queryRules): self
    {
        $this->queryRules = $queryRules;
        return $this;
    }

    /**
     * @return array<string, array<string|Constraint>>
     */
    public function getRequestRules(): array
    {
        return $this->requestRules;
    }

    /**
     * @param array<string, array<string|Constraint>> $requestRules
     */
    public function setRequestRules(array $requestRules): self
    {
        $this->requestRules = $requestRules;
        return $this;
    }
}
