<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation;

use Symfony\Component\Validator\Constraints\Collection;

class ValidationRules
{
    /** @var Collection */
    private $queryRules;

    /** @var Collection */
    private $requestRules;

    public function getQueryRules(): ?Collection
    {
        return $this->queryRules;
    }

    public function setQueryRules(Collection $queryRules): self
    {
        $this->queryRules = $queryRules;
        return $this;
    }

    public function getRequestRules(): ?Collection
    {
        return $this->requestRules;
    }

    public function setRequestRules(Collection $requestRules): self
    {
        $this->requestRules = $requestRules;
        return $this;
    }
}
