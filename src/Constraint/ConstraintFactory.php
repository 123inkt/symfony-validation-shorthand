<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class ConstraintFactory
{
    /**
     * Assert\All should be used instead of Assert\Collection when:
     * - only one rule is defined within rules
     * - the key of the rule is either '*' or '+'  (with/without '?' optional indicator)
     *
     * @param array<string|int, Constraint|array<Constraint>> $rules
     */
    public static function createAllConstraint(array $rules): ?Constraint
    {
        if (count($rules) !== 1) {
            return null;
        }

        $key = key($rules);
        if (is_string($key) === false) {
            return null;
        }

        $optional = false;
        if (str_ends_with($key, '?')) {
            $optional = true;
            $key      = substr($key, -1);
        }

        switch ($key) {
            case '*':
                $constraints = [new Assert\Type('array'), new Assert\All($rules[$key])];
                return $optional ? new Assert\Optional($constraints) : new Assert\Required($constraints);
            case '+':
                $constraints = [new Assert\Type('array'), new Assert\Count(['min' => 1]), new Assert\All($rules[$key])];
                return $optional ? new Assert\Optional($constraints) : new Assert\Required($constraints);
            default:
                return null;
        }
    }
}
