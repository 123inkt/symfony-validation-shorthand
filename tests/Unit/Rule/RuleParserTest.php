<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Unit\Rule;

use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use DigitalRevolution\SymfonyValidationShorthand\Rule\Rule;
use DigitalRevolution\SymfonyValidationShorthand\Rule\RuleList;
use DigitalRevolution\SymfonyValidationShorthand\Rule\RuleParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;

#[CoversClass(RuleParser::class)]
class RuleParserTest extends TestCase
{
    private RuleParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new RuleParser();
    }

    /**
     * @throws InvalidRuleException
     */
    public function testFailParseRuleWithBadRuleType(): void
    {
        $this->expectException(InvalidRuleException::class);
        $this->expectExceptionMessage('Invalid rule definition type. Expecting string or Symfony\Component\Validator\Constraint');
        $this->parser->parseRules(200);
    }

    /**
     * @throws InvalidRuleException
     */
    public function testParseRuleWithSingleConstraint(): void
    {
        $constraint = new Assert\NotBlank();
        $ruleSet    = new RuleList();
        $ruleSet->addRule($constraint);

        static::assertEquals($ruleSet, $this->parser->parseRules($constraint));
    }

    /**
     * @throws InvalidRuleException
     */
    public function testParseRuleWithSingleStringRule(): void
    {
        $ruleSet = new RuleList();
        $ruleSet->addRule(new Rule('required'));

        static::assertEquals($ruleSet, $this->parser->parseRules('required'));
    }

    /**
     * @throws InvalidRuleException
     */
    public function testParseRuleWithSingleStringRuleWithParameter(): void
    {
        $ruleSet = new RuleList();
        $ruleSet->addRule(new Rule('max', ['123']));

        static::assertEquals($ruleSet, $this->parser->parseRules('max:123'));
    }

    /**
     * @throws InvalidRuleException
     */
    public function testParseRuleWithSingleStringRuleWithMultipleParameters(): void
    {
        $ruleSet  = new RuleList();
        $ruleSet->addRule(new Rule('between', ['5', '10']));

        static::assertEquals($ruleSet, $this->parser->parseRules('between:5,10'));
    }

    /**
     * @throws InvalidRuleException
     */
    public function testParseRuleWithSingleStringRuleWithRegexParameter(): void
    {
        $ruleSet  = new RuleList();
        $ruleSet->addRule(new Rule('regex', ['/^\d+$/i']));

        static::assertEquals($ruleSet, $this->parser->parseRules('regex:/^\d+$/i'));
    }

    /**
     * @throws InvalidRuleException
     */
    public function testParseRuleWithMultipleStringRules(): void
    {
        $ruleSet  = new RuleList();
        $ruleSet->addRule(new Rule('required', []));
        $ruleSet->addRule(new Rule('max', ['30']));

        static::assertEquals($ruleSet, $this->parser->parseRules('required|max:30'));
    }

    /**
     * @throws InvalidRuleException
     */
    public function testParseRuleWithMultipleStringRulesAsArray(): void
    {
        $ruleSet  = new RuleList();
        $ruleSet->addRule(new Rule('required', []));
        $ruleSet->addRule(new Rule('max', ['30']));

        static::assertEquals($ruleSet, $this->parser->parseRules(['required', 'max:30']));
    }

    /**
     * @throws InvalidRuleException
     */
    public function testParseRuleWithStringRuleAndConstraint(): void
    {
        $constraint = new Assert\NotBlank();
        $ruleSet    = new RuleList();
        $ruleSet->addRule(new Rule('required', []));
        $ruleSet->addRule($constraint);

        static::assertEquals($ruleSet, $this->parser->parseRules(['required', $constraint]));
    }

    /**
     * @throws InvalidRuleException
     */
    public function testParseRuleWithRuleNormalization(): void
    {
        $ruleSet    = new RuleList();
        $ruleSet->addRule(new Rule('integer', []));
        $ruleSet->addRule(new Rule('boolean', []));

        static::assertEquals($ruleSet, $this->parser->parseRules('int|bool'));
    }
}
