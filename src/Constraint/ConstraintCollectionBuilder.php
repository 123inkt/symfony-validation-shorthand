<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use DigitalRevolution\SymfonyRequestValidation\Utility\Arrays;
use DigitalRevolution\SymfonyRequestValidation\Utility\InvalidArrayPathException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Required;

/**
 * Transform a key-constraint map to a nested set of Assert\All or Assert\Collection constraints.
 */
class ConstraintCollectionBuilder
{
    /**
     * @throws RequestValidationException|InvalidArrayPathException
     */
    public function build(ConstraintMap $constraintsMap): Constraint
    {
        $constraintTreeMap = [];
        foreach ($constraintsMap as $key => $constraints) {
            Arrays::assignToPath($constraintTreeMap, explode('.', $key), $constraints);
        }

        return $this->createConstraintCollection($constraintTreeMap);
    }

    /**
     * @param array<string|int, Constraint|array<Constraint>> $constraintTreeMap
     * @throws RequestValidationException
     */
    private function createConstraintCollection(array $constraintTreeMap): Constraint
    {
        $constraintMap = [];

        // array contains arrays, recursively resolve
        foreach ($constraintTreeMap as $key => $set) {
            if ($set instanceof Constraint === false) {
                $set = $this->createConstraintCollection($set);
            }

            // check for optional
            if (str_ends_with($key, '?')) {
                $key = substr($key, 0, -1);

                // mark this key as optional
                if ($set instanceof Required === false && $set instanceof Optional === false) {
                    $set = new Optional($set);
                }
            }

            $constraintMap[$key] = $set;
        }

        // create Assert\All constraint if needed.
        $constraint = ConstraintHelper::createAllConstraint($constraintMap);
        if ($constraint === null) {
            $constraint = new Collection($constraintMap);
        }

        return $constraint;
    }
}
