<?php
declare(strict_types=1);

namespace Constraint;

use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintCollectionBuilder;
use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintMap;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Required;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintCollectionBuilder
 */
class ConstraintCollectionBuilderTest extends TestCase
{
    /** @var ConstraintCollectionBuilder */
    private $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new ConstraintCollectionBuilder();
    }

    /**
     * @covers ::build
     * @covers ::createConstraintCollection
     * @throws Exception
     */
    public function testBuildSingleNonNestedConstraint(): void
    {
        $constraint    = new NotNull();
        $constraintMap = new ConstraintMap();
        $constraintMap->set('a', $constraint);

        $result = $this->builder->build($constraintMap);
        $expect = new Collection(['a' => new NotNull()]);
        static::assertEquals($expect, $result);
    }

    /**
     * @covers ::build
     * @covers ::createConstraintCollection
     * @throws Exception
     */
    public function testBuildSingleNestedConstraint(): void
    {
        $constraint    = new NotNull();
        $constraintMap = new ConstraintMap();
        $constraintMap->set('a.b', $constraint);

        $result = $this->builder->build($constraintMap);
        $expect = new Collection(['a' => new Collection(['b' => new NotNull()])]);
        static::assertEquals($expect, $result);
    }

    /**
     * @covers ::build
     * @covers ::createConstraintCollection
     * @throws Exception
     */
    public function testBuildMultipleNestedConstraints(): void
    {
        $constraintA   = new NotNull();
        $constraintB   = new Blank();
        $constraintMap = new ConstraintMap();
        $constraintMap->set('a.a', $constraintA);
        $constraintMap->set('a.b', $constraintB);

        $result = $this->builder->build($constraintMap);
        $expect = new Collection([
            'a' => new Collection([
                'a' => new NotNull(),
                'b' => new Blank()
            ])
        ]);
        static::assertEquals($expect, $result);
    }

    /**
     * @covers ::build
     * @covers ::createConstraintCollection
     * @throws Exception
     */
    public function testBuildOptionalConstraints(): void
    {
        $constraint    = new NotNull();
        $constraintMap = new ConstraintMap();
        $constraintMap->set('a?.b', $constraint);

        $result = $this->builder->build($constraintMap);
        $expect = new Collection([
            'a' => new Optional([
                new Collection([
                    'b' => new NotNull()
                ])
            ])
        ]);
        static::assertEquals($expect, $result);
    }

    /**
     * If the constraint is set to required but the path is marked as optional, then always assume Required
     *
     * @covers ::build
     * @covers ::createConstraintCollection
     * @throws Exception
     */
    public function testBuildOptionalConstraintShouldNotOverwriteRequired(): void
    {
        $constraint    = new NotNull();
        $constraintMap = new ConstraintMap();
        $constraintMap->set('a.b?', new Required($constraint));

        $result = $this->builder->build($constraintMap);
        $expect = new Collection([
            'a' => new Collection([
                'b' => new Required(new NotNull())
            ])
        ]);
        static::assertEquals($expect, $result);
    }
}
