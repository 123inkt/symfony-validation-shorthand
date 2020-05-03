<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Constraint;

use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintResolver;
use DigitalRevolution\SymfonyRequestValidation\Parser\Rule;
use DigitalRevolution\SymfonyRequestValidation\Parser\RuleSet;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
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
     * @covers ::resolveRuleSet
     * @covers ::resolveConstraint
     * @throws RequestValidationException
     */
    public function testResolveRuleSetUnknownRule(): void
    {
        $ruleSet = new RuleSet();
        $ruleSet->addRule(new Rule('unknown'));

        $this->expectException(RequestValidationException::class);
        $this->expectExceptionMessage('Unable to resolve rule: unknown');
        $this->resolver->resolveRuleSet($ruleSet);
    }

    /**
     * @dataProvider dataProvider
     * @covers ::resolveRuleSet
     * @covers ::resolveConstraint
     * @param array<Rule|Constraint> $rules
     * @throws RequestValidationException
     */
    public function testResolveRuleSet(Constraint $expected, array $rules): void
    {
        $ruleSet = new RuleSet();
        foreach ($rules as $rule) {
            $ruleSet->addRule($rule);
        }
        static::assertEquals($expected, $this->resolver->resolveRuleSet($ruleSet));
    }

    /**
     * @return Generator<string, array<int, Constraint|Rule[]|Constraint[]>>
     */
    public function dataProvider(): Generator
    {
        yield 'constraint' => [new Assert\Optional(new Assert\NotBlank()), [new Assert\NotBlank()]];
        yield 'boolean' => [new Assert\Optional(new Assert\Type('bool')), [new Rule('boolean')]];
        yield 'integer' => [new Assert\Optional(new Assert\Type('integer')), [new Rule('integer')]];
        yield 'float' => [new Assert\Optional(new Assert\Type('float')), [new Rule('float')]];
        yield 'email' => [new Assert\Optional(new Assert\Email()), [new Rule('email')]];
        yield 'required' => [new Assert\Required(), [new Rule('required')]];
        yield 'required email' => [new Assert\Required(new Assert\Email()), [new Rule('required'), new Rule('email')]];

        // min/max string or array lengths
        yield 'min length' => [new Assert\Optional(new Assert\Length(['min' => 10])), [new Rule('min', ['10'])]];
        yield 'max length' => [new Assert\Optional(new Assert\Length(['max' => 10])), [new Rule('max', ['10'])]];
        yield 'min/max length' => [new Assert\Optional(new Assert\Length(['min' => 10, 'max' => 20])), [new Rule('between', ['10', '20'])]];

        // min/max integer size
        yield 'min integer' => [
            new Assert\Optional([new Assert\Type('integer'), new Assert\Range(['min' => 10])]),
            [new Rule('integer'), new Rule('min', ['10'])]
        ];
        yield 'max integer' => [
            new Assert\Optional([new Assert\Type('integer'), new Assert\Range(['max' => 20])]),
            [new Rule('integer'), new Rule('max', ['20'])]
        ];
        yield 'min/max integer' => [
            new Assert\Optional([new Assert\Type('integer'), new Assert\Range(['min' => 10, 'max' => 20])]),
            [new Rule('integer'), new Rule('between', ['10', '20'])]
        ];
    }
}
