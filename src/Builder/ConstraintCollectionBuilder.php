<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Builder;

use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintHelper;
use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintMap;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use DigitalRevolution\SymfonyRequestValidation\Utility\Arrays;
use DigitalRevolution\SymfonyRequestValidation\Utility\InvalidArrayPathException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;

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
     *
     * @throws RequestValidationException
     */
    private function createConstraintCollection(array $constraintTreeMap): Constraint
    {
        // array contains arrays, recursively resolve
        foreach ($constraintTreeMap as $key => $set) {
            if ($set instanceof Constraint === false) {
                $constraintTreeMap[$key] = $this->createConstraintCollection($set);
            }
        }

        // create Assert\All constraint if needed.
        $constraint = ConstraintHelper::createAllConstraint($constraintTreeMap);
        if ($constraint === null) {
            $constraint = new Collection($constraintTreeMap);
        }

        return $constraint;
    }
}
