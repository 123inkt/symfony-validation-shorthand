<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Constraint;

use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintResolver;
use DigitalRevolution\SymfonyRequestValidation\Constraint\Type\BooleanValue;
use DigitalRevolution\SymfonyRequestValidation\Constraint\Type\FloatNumber;
use DigitalRevolution\SymfonyRequestValidation\Constraint\Type\IntegerNumber;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use DigitalRevolution\SymfonyRequestValidation\Rule\Rule;
use DigitalRevolution\SymfonyRequestValidation\Rule\RuleList;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintResolver
 */
class ConstraintResolverTest extends TestCase
{
    /** @var ConstraintResolver */
    private $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new ConstraintResolver();
    }

    /**
     * @covers ::resolveRuleList
     * @covers ::resolveConstraint
     * @throws RequestValidationException
     */
    public function testResolveRuleSetUnknownRule(): void
    {
        $ruleSet = new RuleList();
        $ruleSet->addRule(new Rule('unknown'));

        $this->expectException(RequestValidationException::class);
        $this->expectExceptionMessage('Unable to resolve rule: unknown');
        $this->resolver->resolveRuleList($ruleSet);
    }

    /**
     * @dataProvider dataProvider
     * @covers ::resolveRuleList
     * @covers ::resolveConstraint
     * @covers ::resolveMinConstraint
     * @covers ::resolveMaxConstraint
     * @covers ::resolveBetweenConstraint
     * @param array<Rule|Constraint> $rules
     * @throws RequestValidationException
     */
    public function testResolveRuleSet(Constraint $expected, array $rules): void
    {
        $ruleSet = new RuleList();
        foreach ($rules as $rule) {
            $ruleSet->addRule($rule);
        }
        static::assertEquals($expected, $this->resolver->resolveRuleList($ruleSet));
    }

    /**
     * @phpstan-return Generator<string, array<int, Constraint|Rule[]|Constraint[]>>
     */
    public function dataProvider(): Generator
    {
        yield 'constraint' => [new Assert\Required(new Assert\NotBlank()), [new Assert\NotBlank()]];
        yield 'rule + constraint' => [
            new Assert\Required([new Assert\NotBlank(), new Assert\NotNull()]),
            [new Rule('required'), new Assert\NotBlank()]
        ];
        yield 'boolean' => [new Assert\Optional([new BooleanValue(), new Assert\NotNull()]), [new Rule('boolean')]];
        yield 'integer' => [new Assert\Optional([new IntegerNumber(), new Assert\NotNull()]), [new Rule('integer')]];
        yield 'float' => [new Assert\Optional([new FloatNumber(), new Assert\NotNull()]), [new Rule('float')]];
        yield 'string' => [new Assert\Optional([new Assert\Type('string'), new Assert\NotNull()]), [new Rule('string')]];
        yield 'email' => [new Assert\Optional([new Assert\Email(), new Assert\NotNull()]), [new Rule('email')]];
        yield 'url' => [new Assert\Optional([new Assert\Url(), new Assert\NotNull()]), [new Rule('url')]];
        yield 'filled' => [new Assert\Optional([new Assert\NotBlank(), new Assert\NotNull()]), [new Rule('filled')]];
        yield 'filled nullable' => [new Assert\Optional([new Assert\NotBlank(['allowNull' => true])]), [new Rule('filled'), new Rule('nullable')]];
        yield 'regex' => [
            new Assert\Optional([new Assert\Regex(['pattern' => '/^unittest$/']), new Assert\NotNull()]),
            [new Rule('regex', ['/^unittest$/'])]
        ];
        yield 'required' => [new Assert\Required(new Assert\NotNull()), [new Rule('required')]];
        yield 'required email' => [new Assert\Required([new Assert\Email(), new Assert\NotNull()]), [new Rule('required'), new Rule('email')]];

        // min/max string or array lengths
        yield 'min length' => [new Assert\Optional([new Assert\Length(['min' => 10]), new Assert\NotNull()]), [new Rule('min', ['10'])]];
        yield 'max length' => [new Assert\Optional([new Assert\Length(['max' => 10]), new Assert\NotNull()]), [new Rule('max', ['10'])]];
        yield 'min/max length' => [
            new Assert\Optional([new Assert\Length(['min' => 10, 'max' => 20]), new Assert\NotNull()]),
            [new Rule('between', ['10', '20'])]
        ];

        // min/max integer size
        yield 'min integer' => [
            new Assert\Optional([new IntegerNumber(), new Assert\GreaterThanOrEqual(10), new Assert\NotNull()]),
            [new Rule('integer'), new Rule('min', ['10'])]
        ];
        yield 'max integer' => [
            new Assert\Optional([new IntegerNumber(), new Assert\LessThanOrEqual(20), new Assert\NotNull()]),
            [new Rule('integer'), new Rule('max', ['20'])]
        ];
        yield 'min/max integer' => [
            new Assert\Optional([new IntegerNumber(), new Assert\Range(['min' => 10, 'max' => 20]), new Assert\NotNull()]),
            [new Rule('integer'), new Rule('between', ['10', '20'])]
        ];
    }
}
