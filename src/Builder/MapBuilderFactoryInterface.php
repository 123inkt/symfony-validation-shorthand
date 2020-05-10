<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Builder;

interface MapBuilderFactoryInterface
{
    /**
     * Create a builder that transforms rule definitions to a RuleListMap
     */
    public function createRuleListMapBuilder(): RuleListMapBuilder;

    /**
     * Create a builder that transforms RuleListMap to ConstraintMap
     */
    public function createConstraintMapBuilder(): ConstraintMapBuilder;

    /**
     * Create a builder that transforms a ConstraintMap to a Collection constraint based on the keys of the map
     */
    public function createConstraintCollectionBuilder(): ConstraintCollectionBuilder;
}
