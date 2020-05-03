<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Parser;

use DigitalRevolution\SymfonyRequestValidation\Constraint\ConstraintResolver;
use DigitalRevolution\SymfonyRequestValidation\Parser\Rule;
use DigitalRevolution\SymfonyRequestValidation\Parser\RuleSet;
use DigitalRevolution\SymfonyRequestValidation\Parser\ValidationRuleParser;
use DigitalRevolution\SymfonyRequestValidation\RequestValidationException;
use DigitalRevolution\SymfonyRequestValidation\Tests\Mock\ConstraintResolverMockHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Parser\ValidationRuleParser
 */
class ValidationRuleParserTest extends TestCase
{
    /** @var ValidationRuleParser */
    private $parser;

    /** @var ConstraintResolverMockHelper */
    private $resolverMockHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $resolver                 = $this->createMock(ConstraintResolver::class);
        $this->resolverMockHelper = new ConstraintResolverMockHelper($resolver);
        $this->parser             = new ValidationRuleParser($resolver);
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @throws RequestValidationException
     */
    public function testFailParseInvalidFieldName(): void
    {
        $this->expectException(RequestValidationException::class);
        $this->expectExceptionMessage('Field names should be string. Field type is: ');
        $this->parser->parse([1 => 'a']);
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @throws RequestValidationException
     */
    public function testFailParseRuleWithBadRuleType(): void
    {
        $this->expectException(RequestValidationException::class);
        $this->expectExceptionMessage('Invalid rule definition type. Expecting string or constraint');
        $this->parser->parse(['username' => [200]]);
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @throws RequestValidationException
     */
    public function testParseRuleWithSingleConstraint(): void
    {
        $constraint = new Assert\NotBlank();
        $optional   = new Assert\Optional($constraint);
        $ruleSet    = new RuleSet();
        $ruleSet->addRule($constraint);

        $this->resolverMockHelper->mockResolveRuleSet($ruleSet, $optional);
        $this->assertCollection(['username' => $optional], $this->parser->parse(['username' => $constraint]));
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @throws RequestValidationException
     */
    public function testParseRuleWithSingleStringRule(): void
    {
        $required = new Assert\Required();
        $ruleSet  = new RuleSet();
        $ruleSet->addRule(new Rule('required'));

        $this->resolverMockHelper->mockResolveRuleSet($ruleSet, $required);
        $this->assertCollection(['username' => $required], $this->parser->parse(['username' => 'required']));
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithSingleStringRuleWithParameter(): void
    {
        $optional = new Assert\Optional();
        $ruleSet  = new RuleSet();
        $ruleSet->addRule(new Rule('max', ['123']));

        $this->resolverMockHelper->mockResolveRuleSet($ruleSet, $optional);
        $this->assertCollection(['username' => $optional], $this->parser->parse(['username' => 'max:123']));
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithSingleStringRuleWithMultipleParameters(): void
    {
        $optional = new Assert\Optional();
        $ruleSet  = new RuleSet();
        $ruleSet->addRule(new Rule('between', ['5', '10']));

        $this->resolverMockHelper->mockResolveRuleSet($ruleSet, $optional);
        $this->assertCollection(['username' => $optional], $this->parser->parse(['username' => 'between:5,10']));
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithSingleStringRuleWithRegexParameter(): void
    {
        $optional = new Assert\Optional();
        $ruleSet  = new RuleSet();
        $ruleSet->addRule(new Rule('regex', ['/^\d+$/i']));

        $this->resolverMockHelper->mockResolveRuleSet($ruleSet, $optional);
        $this->assertCollection(['phone-number' => $optional], $this->parser->parse(['phone-number' => 'regex:/^\d+$/i']));
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithMultipleStringRules(): void
    {
        $optional = new Assert\Optional();
        $ruleSet  = new RuleSet();
        $ruleSet->addRule(new Rule('required', []));
        $ruleSet->addRule(new Rule('max', ['30']));

        $this->resolverMockHelper->mockResolveRuleSet($ruleSet, $optional);
        $this->assertCollection(['username' => $optional], $this->parser->parse(['username' => 'required|max:30']));
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithMultipleStringRulesAsArray(): void
    {
        $optional = new Assert\Optional();
        $ruleSet  = new RuleSet();
        $ruleSet->addRule(new Rule('required', []));
        $ruleSet->addRule(new Rule('max', ['30']));

        $this->resolverMockHelper->mockResolveRuleSet($ruleSet, $optional);
        $this->assertCollection(['username' => $optional], $this->parser->parse(['username' => ['required', 'max:30']]));
    }

    /**
     * @covers ::parse
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws RequestValidationException
     */
    public function testParseRuleWithStringRuleAndConstraint(): void
    {
        $constraint = new Assert\NotBlank();
        $optional   = new Assert\Optional();
        $ruleSet    = new RuleSet();
        $ruleSet->addRule(new Rule('required', []));
        $ruleSet->addRule($constraint);

        $this->resolverMockHelper->mockResolveRuleSet($ruleSet, $optional);
        $this->assertCollection(['username' => $optional], $this->parser->parse(['username' => ['required', $constraint]]));
    }

    private function assertCollection(array $fields, Collection $actual, string $message = ''): void
    {
        static::assertEquals(new Assert\Collection(['fields' => $fields]), $actual, $message);
    }
}
