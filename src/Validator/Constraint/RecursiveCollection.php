<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Required;

class RecursiveCollection extends Constraint
{
    /** @var array<string, Optional|Required> */
    public $fields = [];

    public function __construct(array $constraints)
    {
        // wrap every constraint is wrapped in required or optional
        foreach ($constraints as $key => $constraint) {
            if ($constraint instanceof Optional === false && $constraint instanceof Required === false) {
                $constraints[$key] = new Required($constraint);
            }
        }

        parent::__construct(['fields' => $constraints]);
    }

    public function getRequiredOptions(): array
    {
        return ['fields'];
    }
}
