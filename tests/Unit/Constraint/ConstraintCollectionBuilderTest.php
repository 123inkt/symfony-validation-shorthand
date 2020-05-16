<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Unit\Constraint;

use DigitalRevolution\SymfonyValidationShorthand\Constraint\ConstraintCollectionBuilder;
use DigitalRevolution\SymfonyValidationShorthand\Constraint\ConstraintMap;
use DigitalRevolution\SymfonyValidationShorthand\Constraint\ConstraintMapItem;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Required;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyValidationShorthand\Constraint\ConstraintCollectionBuilder
 * @covers ::createConstraintTree
 * @covers ::createAllConstraint
 * @covers ::createCollectionConstraint
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
     * @throws Exception
     */
    public function testBuildSingleNonNestedConstraint(): void
    {
        $constraint    = new NotNull();
        $constraintMap = new ConstraintMap();
        $constraintMap->set('a', new ConstraintMapItem([$constraint], true));

        $result = $this->builder->build($constraintMap);
        $expect = new Collection(['a' => new NotNull()]);
        static::assertEquals($expect, $result);
    }

    /**
     * @covers ::build
     * @throws Exception
     */
    public function testBuildSingleNestedConstraint(): void
    {
        $constraint    = new NotNull();
        $constraintMap = new ConstraintMap();
        $constraintMap->set('a.b', new ConstraintMapItem([$constraint], true));

        $result = $this->builder->build($constraintMap);
        $expect = new Collection(['a' => new Collection(['b' => new NotNull()])]);
        static::assertEquals($expect, $result);
    }

    /**
     * @covers ::build
     * @throws Exception
     */
    public function testBuildMultipleNestedConstraints(): void
    {
        $constraintA   = new NotNull();
        $constraintB   = new Blank();
        $constraintMap = new ConstraintMap();
        $constraintMap->set('a.a', new ConstraintMapItem([$constraintA], true));
        $constraintMap->set('a.b', new ConstraintMapItem([$constraintB], true));

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
     * @throws Exception
     */
    public function testBuildOptionalConstraints(): void
    {
        $constraint    = new NotNull();
        $constraintMap = new ConstraintMap();
        $constraintMap->set('a?.b', new ConstraintMapItem([$constraint], true));

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
     * @throws Exception
     */
    public function testBuildOptionalConstraintShouldNotOverwriteRequired(): void
    {
        $constraint    = new NotNull();
        $constraintMap = new ConstraintMap();
        $constraintMap->set('a.b?', new ConstraintMapItem([$constraint], true));

        $result = $this->builder->build($constraintMap);
        $expect = new Collection([
            'a' => new Collection([
                'b' => new Required(new NotNull())
            ])
        ]);
        static::assertEquals($expect, $result);
    }

    /**
     * If the constraint is set to required but the path is marked as optional, then always assume Required
     *
     * @covers ::build
     * @throws Exception
     */
    public function testBuildWithNonEmptyAllConstraint(): void
    {
        $constraint    = new NotNull();
        $constraintMap = new ConstraintMap();
        $constraintMap->set('*', new ConstraintMapItem([$constraint], true));

        $result = $this->builder->build($constraintMap);
        $expect = [
            new Count(['min' => 1]),
            new All([new NotNull()])
        ];
        static::assertEquals($expect, $result);
    }

    /**
     * If the constraint is set to required but the path is marked as optional, then always assume Required
     *
     * @covers ::build
     * @throws Exception
     */
    public function testBuildWithEmptyAllConstraint(): void
    {
        $constraint    = new NotNull();
        $constraintMap = new ConstraintMap();
        $constraintMap->set('*', new ConstraintMapItem([$constraint], false));

        $result = $this->builder->build($constraintMap);
        $expect = new All([new NotNull()]);
        static::assertEquals($expect, $result);
    }
}
