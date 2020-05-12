<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class ConstraintHelper
{
    /**
     * Assert\All should be used instead of Assert\Collection when:
     * - only one rule is defined within rules
     * - the key of the rule is '*'
     *
     * @param array<string|int, Constraint|array<Constraint>> $rules
     */
    public static function createAllConstraint(array $rules): ?Constraint
    {
        if (count($rules) !== 1) {
            return null;
        }

        $key = key($rules);
        if ($key !== '*') {
            return null;
        }

        return new Assert\All($rules[$key]);
    }
}
