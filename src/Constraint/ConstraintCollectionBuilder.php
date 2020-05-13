<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use DigitalRevolution\SymfonyRequestValidation\Utility\Arrays;
use DigitalRevolution\SymfonyRequestValidation\Utility\InvalidArrayPathException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

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

        return $this->createConstraintTree($constraintTreeMap);
    }

    /**
     * @param array<string|int, ConstraintMapItem|array<ConstraintMapItem>> $constraintTreeMap
     * @throws RequestValidationException
     */
    private function createConstraintTree(array $constraintTreeMap): Constraint
    {
        if (count($constraintTreeMap) === 1 && isset($constraintTreeMap['*'])) {
            return $this->createAllConstraint($constraintTreeMap['*']);
        }

        return $this->createCollectionConstraint($constraintTreeMap);
    }

    /**
     * @param ConstraintMapItem|array<ConstraintMapItem> $node
     * @throws RequestValidationException
     */
    private function createAllConstraint($node): Assert\All
    {
        if ($node instanceof ConstraintMapItem) {
            $constraints = $node->getConstraints();
            if ($node->isRequired()) {
                if (is_array($constraints) === false) {
                    $constraints = [$constraints];
                }
                array_unshift($constraints, new Assert\Count(1));
            }
        } else {
            $constraints = $this->createConstraintTree($node);
        }

        return new Assert\All($constraints);
    }

    /**
     * @param array<string|int, ConstraintMapItem|array<ConstraintMapItem>> $constraintTreeMap
     * @throws RequestValidationException
     */
    private function createCollectionConstraint(array $constraintTreeMap): Assert\Collection
    {
        $constraintMap = [];

        // array contains arrays, recursively resolve
        foreach ($constraintTreeMap as $key => $node) {
            $optional = false;
            // key is marked as optional
            if (is_string($key) && str_ends_with($key, '?')) {
                $key      = substr($key, 0, -1);
                $optional = true;
            }

            if ($node instanceof ConstraintMapItem === false) {
                // recursively resolve
                $constraint = $this->createConstraintTree($node);
            } else {
                // leaf node, check for required. It should over rule any optional indicators in the key
                $constraint = $node->getConstraints();
                $optional   = $node->isRequired() === false;
            }

            // optional key
            if ($optional && $constraint instanceof Assert\Required === false && $constraint instanceof Assert\Optional === false) {
                $constraint = new Assert\Optional($constraint);
            }

            $constraintMap[$key] = $constraint;
        }

        return new Assert\Collection($constraintMap);
    }
}
