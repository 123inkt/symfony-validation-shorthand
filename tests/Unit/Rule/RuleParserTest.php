<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyValidationShorthand\Tests\Unit\Rule;

use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use DigitalRevolution\SymfonyValidationShorthand\Rule\Rule;
use DigitalRevolution\SymfonyValidationShorthand\Rule\RuleList;
use DigitalRevolution\SymfonyValidationShorthand\Rule\RuleParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyValidationShorthand\Rule\RuleParser
 */
class RuleParserTest extends TestCase
{
    private RuleParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new RuleParser();
    }

    /**
     * @covers ::parseRules
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
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::normalizeRuleName
     * @throws InvalidRuleException
     */
    public function testParseRuleWithSingleStringRule(): void
    {
        $ruleSet = new RuleList();
        $ruleSet->addRule(new Rule('required'));

        static::assertEquals($ruleSet, $this->parser->parseRules('required'));
    }

    /**
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws InvalidRuleException
     */
    public function testParseRuleWithSingleStringRuleWithParameter(): void
    {
        $ruleSet = new RuleList();
        $ruleSet->addRule(new Rule('max', ['123']));

        static::assertEquals($ruleSet, $this->parser->parseRules('max:123'));
    }

    /**
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws InvalidRuleException
     */
    public function testParseRuleWithSingleStringRuleWithMultipleParameters(): void
    {
        $ruleSet  = new RuleList();
        $ruleSet->addRule(new Rule('between', ['5', '10']));

        static::assertEquals($ruleSet, $this->parser->parseRules('between:5,10'));
    }

    /**
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
     * @throws InvalidRuleException
     */
    public function testParseRuleWithSingleStringRuleWithRegexParameter(): void
    {
        $ruleSet  = new RuleList();
        $ruleSet->addRule(new Rule('regex', ['/^\d+$/i']));

        static::assertEquals($ruleSet, $this->parser->parseRules('regex:/^\d+$/i'));
    }

    /**
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
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
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
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
     * @covers ::parseRules
     * @covers ::explodeExplicitRule
     * @covers ::parseStringRule
     * @covers ::parseParameters
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
     * @covers ::normalizeRuleName
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
