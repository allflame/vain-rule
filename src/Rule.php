<?php
/**
 * Vain Framework
 *
 * PHP Version 7
 *
 * @package   vain-expression
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://github.com/allflame/vain-expression
 */
declare(strict_types = 1);

namespace Vain\Rule;

use Vain\Core\Result\ResultInterface;
use Vain\Expression\Boolean\BooleanExpressionInterface;
use Vain\Expression\ExpressionInterface;
use Vain\Rule\Result\RuleResult;

/**
 * Class Rule
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class Rule implements RuleInterface
{
    private $name;

    private $expression;

    /**
     * Rule constructor.
     *
     * @param string                     $name
     * @param BooleanExpressionInterface $expression
     */
    public function __construct(string $name, BooleanExpressionInterface $expression)
    {
        $this->name = $name;
        $this->expression = $expression;
    }

    /**
     * @inheritDoc
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return ExpressionInterface
     */
    public function getExpression() : ExpressionInterface
    {
        return $this->expression;
    }

    /**
     * @inheritDoc
     */
    public function interpret(\ArrayAccess $context = null) : ResultInterface
    {
        return new RuleResult($this, $this->expression->interpret($context));
    }

    /**
     * @inheritDoc
     */
    public function __toString() : string
    {
        return sprintf('(%s) as %s', $this->name, $this->expression);
    }

    /**
     * @inheritDoc
     */
    public function toArray() : array
    {
        return ['rule' => ['name' => $this->name, 'expression' => $this->expression->toArray()]];
    }
}