<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Constraint;

use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintResolver;
use DigitalRevolution\SymfonyRequestValidation\Parser\Rule;
use DigitalRevolution\SymfonyRequestValidation\Parser\RuleList;
use DigitalRevolution\SymfonyRequestValidation\Parser\ValidationRuleParser;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

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
     * @throws RequestValidationException
     */
    public function testPlayground2(): void
    {
        $validator = Validation::createValidator();

        $input = [
            'name'      => [
                'first_name' => 'Fabien',
                'last_name'  => 'Potencier',
            ],
            'email'     => 'test@email.tld',
            'simple'    => 'hello',
            'eye_color' => 3,
            'file'      => null,
            'password'  => 'test',
            'tags'      => [
                [
                    'slug'  => 'symfony_doc',
                    'label' => 'symfony doc',
                ],
            ],
        ];

        $constraintResolver = new ConstraintResolver();
        $parser             = new ValidationRuleParser();
        $ruleSet            = $parser->parseRules(['required']);


        $constraint = $constraintResolver->resolveRuleList($ruleSet);
        $collection = new Assert\Collection(['first_name' => $constraint]);

        $violations = $validator->validate(['first_name' => null], $collection);
        static::assertCount(0, $violations);

        $rules = [
            'name.first_name' => 'required|min:6',
            'name.last_name'  => 'min:1',
            'email'           => 'required|email',
            'simple'          => 'required|min:5',
            'eye_color'       => 'required|enum:3,4',
            'file'            => 'required|file',
            'password'        => 'required|min:60',
            'tags?.*.slug'     => 'required|filled',
            'tags?.*.label'    => 'required|filled',
        ];

        $constraint = new Assert\Collection([
            // the keys correspond to the keys in the input array
            'name'      => new Assert\Collection([
                'first_name' => new Assert\Length(['min' => 6]),
                'last_name'  => new Assert\Optional(new Assert\Length(['min' => 1])),
            ]),
            'email'     => new Assert\Email(),
            'simple'    => new Assert\Length(['min' => 5]),
            'eye_color' => new Assert\Choice([3, 4]),
            'file'      => new Assert\File(),
            'password'  => new Assert\Length(['min' => 4]),
            'tags'      => new Assert\Optional([
                new Assert\Type('array'),
                new Assert\Count(['min' => 1]),
                new Assert\All([
                    new Assert\Collection([
                        'slug'  => [
                            new Assert\NotBlank(),
                            new Assert\Type(['type' => 'string'])
                        ],
                        'label' => [
                            new Assert\NotBlank(),
                        ],
                    ]),
                ]),
            ]),
        ]);
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
        yield 'constraint' => [new Assert\Optional(new Assert\NotBlank()), [new Assert\NotBlank()]];
        yield 'boolean' => [new Assert\Optional(new Assert\Type('bool')), [new Rule('boolean')]];
        yield 'integer' => [new Assert\Optional(new Assert\Type('integer')), [new Rule('integer')]];
        yield 'float' => [new Assert\Optional(new Assert\Type('float')), [new Rule('float')]];
        yield 'email' => [new Assert\Optional(new Assert\Email()), [new Rule('email')]];
        yield 'regex' => [new Assert\Optional(new Assert\Regex(['pattern' => '/^unittest$/'])), [new Rule('regex', ['/^unittest$/'])]];
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
