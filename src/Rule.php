<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 6/10/16
 * Time: 9:48 AM
 */

namespace Vain\Rule;

use Vain\Expression\Boolean\BooleanExpressionInterface;
use Vain\Expression\ExpressionInterface;
use Vain\Rule\Result\RuleResult;

class Rule implements RuleInterface
{
    private $name;

    private $expression;

    /**
     * Rule constructor.
     * @param string $name
     * @param BooleanExpressionInterface $expression
     */
    public function __construct($name, BooleanExpressionInterface $expression)
    {
        $this->name = $name;
        $this->expression = $expression;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ExpressionInterface
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @inheritDoc
     */
    public function interpret(\ArrayAccess $context = null)
    {
        return new RuleResult($this, $this->expression->interpret($context));
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return sprintf('(%s) as %s', $this->name, $this->expression);
    }
}