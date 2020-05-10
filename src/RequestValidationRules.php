<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;

class RequestValidationRules
{
    /** @var Collection|array<string, string|Constraint|array<string|Constraint>>|null */
    private $queryRules;

    /** @var Collection|array<string, string|Constraint|array<string|Constraint>>|null */
    private $requestRules;

    /**
     * @return Collection|array<string, string|Constraint|array<string|Constraint>>|null
     */
    public function getQueryRules()
    {
        return $this->queryRules;
    }

    /**
     * @param Collection|array<string, string|Constraint|array<string|Constraint>>|null $queryRules
     */
    public function setQueryRules($queryRules): self
    {
        $this->queryRules = $queryRules;
        return $this;
    }

    /**
     * @return Collection|array<string, string|Constraint|array<string|Constraint>>|null
     */
    public function getRequestRules()
    {
        return $this->requestRules;
    }

    /**
     * @param Collection|array<string, string|Constraint|array<string|Constraint>>|null $requestRules
     */
    public function setRequestRules($requestRules): self
    {
        $this->requestRules = $requestRules;
        return $this;
    }
}
