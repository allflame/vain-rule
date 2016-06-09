<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 4/7/16
 * Time: 12:29 PM
 */

namespace Vain\Rule\Exception;

use Vain\Expression\NonTerminal\FunctionX\FunctionExpression;
use Vain\Rule\Evaluator\EvaluatorInterface;

class UnknownFunctionException extends ExpressionEvaluatorException
{
    /**
     * UnknownFunctionException constructor.
     * @param EvaluatorInterface $evaluator
     * @param FunctionExpression $expression
     */
    public function __construct(EvaluatorInterface $evaluator, FunctionExpression $expression)
    {
        parent::__construct($evaluator, $expression, sprintf('Function %s is not registered', $expression->getFunctionName()), 0, null);
    }
}