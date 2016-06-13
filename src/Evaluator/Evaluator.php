<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 6/13/16
 * Time: 12:05 PM
 */

namespace Vain\Rule\Evaluator;


use Vain\Expression\Boolean\AndX\AndExpression;
use Vain\Expression\Boolean\Equal\EqualExpression;
use Vain\Expression\Boolean\False\FalseExpression;
use Vain\Expression\Boolean\Greater\GreaterExpression;
use Vain\Expression\Boolean\GreaterOrEqual\GreaterOrEqualExpression;
use Vain\Expression\Boolean\Identity\IdentityExpression;
use Vain\Expression\Boolean\In\InExpression;
use Vain\Expression\Boolean\Less\LessExpression;
use Vain\Expression\Boolean\LessOrEqual\LessOrEqualExpression;
use Vain\Expression\Boolean\Like\LikeExpression;
use Vain\Expression\Boolean\Not\NotExpression;
use Vain\Expression\Boolean\NotEqual\NotEqualExpression;
use Vain\Expression\Boolean\OrX\OrExpression;
use Vain\Expression\Boolean\True\TrueExpression;
use Vain\Expression\Interpreter\InterpreterInterface;
use Vain\Expression\NonTerminal\Filter\FilterExpression;
use Vain\Expression\NonTerminal\FunctionX\FunctionExpression;
use Vain\Expression\NonTerminal\Helper\HelperExpression;
use Vain\Expression\NonTerminal\Method\MethodExpression;
use Vain\Expression\NonTerminal\Mode\ModeExpression;
use Vain\Expression\NonTerminal\Module\ModuleExpression;
use Vain\Expression\NonTerminal\Property\PropertyExpression;
use Vain\Expression\Terminal\Context\ContextExpression;
use Vain\Expression\Terminal\InPlace\InPlaceExpression;
use Vain\Rule\Result\RuleResult;
use Vain\Rule\RuleInterface;
use Vain\Rule\Visitor\RuleVisitorInterface;

class Evaluator implements RuleVisitorInterface, InterpreterInterface
{
    private $interpreter;

    /**
     * RuleEvaluator constructor.
     * @param InterpreterInterface $interpreter
     */
    public function __construct(InterpreterInterface $interpreter)
    {
        $this->interpreter = $interpreter;
    }

    /**
     * @inheritDoc
     */
    public function eq(EqualExpression $equalExpression)
    {
        return $this->interpreter->eq($equalExpression);
    }

    /**
     * @inheritDoc
     */
    public function neq(NotEqualExpression $notEqualExpression)
    {
        return $this->interpreter->neq($notEqualExpression);
    }

    /**
     * @inheritDoc
     */
    public function gt(GreaterExpression $greaterExpression)
    {
        return $this->interpreter->gt($greaterExpression);
    }

    /**
     * @inheritDoc
     */
    public function gte(GreaterOrEqualExpression $greaterOrEqualExpression)
    {
        return $this->interpreter->gte($greaterOrEqualExpression);
    }

    /**
     * @inheritDoc
     */
    public function lt(LessExpression $lessExpression)
    {
        return $this->interpreter->lt($lessExpression);
    }

    /**
     * @inheritDoc
     */
    public function lte(LessOrEqualExpression $lessOrEqualExpression)
    {
        return $this->interpreter->lte($lessOrEqualExpression);
    }

    /**
     * @inheritDoc
     */
    public function in(InExpression $inExpression)
    {
        return $this->interpreter->in($inExpression);
    }

    /**
     * @inheritDoc
     */
    public function like(LikeExpression $likeExpression)
    {
        return $this->interpreter->like($likeExpression);
    }

    /**
     * @inheritDoc
     */
    public function true(TrueExpression $trueExpression)
    {
        return $this->interpreter->true($trueExpression);
    }

    /**
     * @inheritDoc
     */
    public function false(FalseExpression $falseExpression)
    {
        return $this->interpreter->false($falseExpression);
    }

    /**
     * @inheritDoc
     */
    public function id(IdentityExpression $identityExpression)
    {
        return $this->interpreter->id($identityExpression);
    }

    /**
     * @inheritDoc
     */
    public function not(NotExpression $notExpression)
    {
        return $this->interpreter->not($notExpression);
    }

    /**
     * @inheritDoc
     */
    public function andX(AndExpression $andExpression)
    {
        return $this->interpreter->andX($andExpression);
    }

    /**
     * @inheritDoc
     */
    public function orX(OrExpression $orExpression)
    {
        return $this->interpreter->orX($orExpression);
    }

    /**
     * @inheritDoc
     */
    public function inPlace(InPlaceExpression $inPlaceExpression)
    {
        return $this->interpreter->inPlace($inPlaceExpression);
    }

    /**
     * @inheritDoc
     */
    public function module(ModuleExpression $moduleExpression)
    {
        return $this->interpreter->module($moduleExpression);
    }

    /**
     * @inheritDoc
     */
    public function method(MethodExpression $methodExpression)
    {
        return $this->interpreter->method($methodExpression);
    }

    /**
     * @inheritDoc
     */
    public function property(PropertyExpression $propertyExpression)
    {
        return $this->interpreter->property($propertyExpression);
    }

    /**
     * @inheritDoc
     */
    public function functionX(FunctionExpression $functionExpression)
    {
        return $this->interpreter->functionX($functionExpression);
    }

    /**
     * @inheritDoc
     */
    public function mode(ModeExpression $modeExpression)
    {
        return $this->interpreter->mode($modeExpression);
    }

    /**
     * @inheritDoc
     */
    public function filter(FilterExpression $filterExpression)
    {
        return $this->interpreter->filter($filterExpression);
    }

    /**
     * @inheritDoc
     */
    public function helper(HelperExpression $helperExpression)
    {
        return $this->interpreter->helper($helperExpression);
    }

    /**
     * @inheritDoc
     */
    public function context(ContextExpression $contextExpression)
    {
        return $this->interpreter->context($contextExpression);
    }

    /**
     * @inheritDoc
     */
    public function withContext(\ArrayAccess $context = null)
    {
        $copy = clone $this;
        $copy->interpreter = $copy->interpreter->withContext($context);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function rule(RuleInterface $rule)
    {
        return new RuleResult($rule, $rule->getExpression()->accept($this->interpreter));
    }
}