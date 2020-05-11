<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Builder;

use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintResolver;
use DigitalRevolution\SymfonyRequestValidation\Rule\RuleParser;

class MapBuilderFactory implements MapBuilderFactoryInterface
{
    /** @var RuleParser|null */
    private $parser;

    /** @var ConstraintResolver|null */
    private $resolver;

    public function __construct(RuleParser $parser = null, ConstraintResolver $resolver = null)
    {
        $this->parser   = $parser ?? new RuleParser();
        $this->resolver = $resolver ?? new ConstraintResolver();
    }

    /**
     * @inheritDoc
     */
    public function createRuleListMapBuilder(): RuleListMapBuilder
    {
        return new RuleListMapBuilder($this->parser);
    }

    /**
     * @inheritDoc
     */
    public function createConstraintMapBuilder(): ConstraintMapBuilder
    {
        return new ConstraintMapBuilder($this->resolver);
    }

    /**
     * @inheritDoc
     */
    public function createConstraintCollectionBuilder(): ConstraintCollectionBuilder
    {
        return new ConstraintCollectionBuilder();
    }
}
