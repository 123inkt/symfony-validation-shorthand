<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Unit\Constraint;

use DigitalRevolution\SymfonyValidationShorthand\Constraint\ConstraintResolver;
use DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\BooleanValue;
use DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\FloatNumber;
use DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\InConstraint;
use DigitalRevolution\SymfonyValidationShorthand\Constraint\Type\IntegerNumber;
use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use DigitalRevolution\SymfonyValidationShorthand\Rule\Rule;
use DigitalRevolution\SymfonyValidationShorthand\Rule\RuleList;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyValidationShorthand\Constraint\ConstraintResolver
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @throws InvalidRuleException
     */
    public function testResolveRuleSetUnknownRule(): void
    {
        $ruleSet = new RuleList();
        $ruleSet->addRule(new Rule('unknown'));

        $this->expectException(InvalidRuleException::class);
        $this->expectExceptionMessage("Unable to resolve rule: 'unknown'");
        $this->resolver->resolveRuleList($ruleSet);
    }

    /**
     * @dataProvider dataProvider
     * @covers ::resolveRuleList
     * @covers ::resolveConstraint
     * @covers ::resolveMinConstraint
     * @covers ::resolveMaxConstraint
     * @covers ::resolveBetweenConstraint
     * @param Constraint[]
     * @param array<Rule|Constraint> $rules
     * @throws InvalidRuleException
     */
    public function testResolveRuleSet(array $expected, array $rules): void
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
        yield 'constraint' => [[new Assert\NotBlank()], [new Assert\NotBlank()]];
        yield 'rule + constraint' => [[new Assert\NotBlank(), new Assert\NotNull()],[new Rule('required'), new Assert\NotBlank()]];
        yield 'boolean' => [[new BooleanValue(), new Assert\NotNull()], [new Rule('boolean')]];
        yield 'integer' => [[new IntegerNumber(), new Assert\NotNull()], [new Rule('integer')]];
        yield 'float' => [[new FloatNumber(), new Assert\NotNull()], [new Rule('float')]];
        yield 'array' => [[new Assert\Type('array'), new Assert\NotNull()], [new Rule('array')]];
        yield 'string' => [[new Assert\Type('string'), new Assert\NotNull()], [new Rule('string')]];
        yield 'alpha' => [[new Assert\Regex(['pattern' => '/^[a-zA-Z]*$/']), new Assert\NotNull()], [new Rule('alpha')]];
        yield 'alpha_dash' => [[new Assert\Regex(['pattern' => '/^[\w-]*$/']), new Assert\NotNull()], [new Rule('alpha_dash')]];
        yield 'alpha_num' => [[new Assert\Regex(['pattern' => '/^[a-zA-Z0-9]*$/']), new Assert\NotNull()], [new Rule('alpha_num')]];
        yield 'in' => [[new InConstraint(['values' => ['a', 'b']]), new Assert\NotNull()], [new Rule('in', ['a', 'b'])]];
        yield 'email' => [[new Assert\Email(), new Assert\NotNull()], [new Rule('email')]];
        yield 'url' => [[new Assert\Url(), new Assert\NotNull()], [new Rule('url')]];
        yield 'filled' => [[new Assert\NotBlank(), new Assert\NotNull()], [new Rule('filled')]];
        yield 'filled nullable' => [[new Assert\NotBlank(['allowNull' => true])], [new Rule('filled'), new Rule('nullable')]];
        yield 'regex' => [
            [new Assert\Regex(['pattern' => '/^unittest$/']), new Assert\NotNull()],
            [new Rule('regex', ['/^unittest$/'])]
        ];
        yield 'required' => [[new Assert\NotNull()], [new Rule('required')]];
        yield 'required email' => [[new Assert\Email(), new Assert\NotNull()], [new Rule('required'), new Rule('email')]];

        // date, datetime, date_format
        yield 'date' => [[new Assert\Date(), new Assert\NotNull()], [new Rule('date')]];
        yield 'datetime' => [[new Assert\DateTime(), new Assert\NotNull()], [new Rule('datetime')]];
        yield 'date_format' => [[new Assert\DateTime(['format' => 'd/m/Y']), new Assert\NotNull()], [new Rule('date_format', ['d/m/Y'])]];

        // min/max string or array lengths
        yield 'min length' => [[new Assert\Length(['min' => 10]), new Assert\NotNull()], [new Rule('min', ['10'])]];
        yield 'max length' => [[new Assert\Length(['max' => 10]), new Assert\NotNull()], [new Rule('max', ['10'])]];
        yield 'min/max length' => [
            [new Assert\Length(['min' => 10, 'max' => 20]), new Assert\NotNull()],
            [new Rule('between', ['10', '20'])]
        ];

        // min/max integer size
        yield 'min integer' => [
            [new IntegerNumber(), new Assert\GreaterThanOrEqual(10), new Assert\NotNull()],
            [new Rule('integer'), new Rule('min', ['10'])]
        ];
        yield 'max integer' => [
            [new IntegerNumber(), new Assert\LessThanOrEqual(20), new Assert\NotNull()],
            [new Rule('integer'), new Rule('max', ['20'])]
        ];
        yield 'min/max integer' => [
            [new IntegerNumber(), new Assert\Range(['min' => 10, 'max' => 20]), new Assert\NotNull()],
            [new Rule('integer'), new Rule('between', ['10', '20'])]
        ];
    }
}
