<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 4/5/16
 * Time: 6:31 PM
 */

namespace Vain\Rule\Exception;

use Vain\Expression\Unary\Method\MethodExpression;
use Vain\Rule\Evaluator\EvaluatorInterface;

class UnknownMethodException extends ExpressionEvaluatorException
{
    /**
     * UnknownMethodException constructor.
     * @param EvaluatorInterface $evaluator
     * @param MethodExpression $expression
     */
    public function __construct(EvaluatorInterface $evaluator, MethodExpression $expression)
    {
        parent::__construct($evaluator, $expression, sprintf('Method %s does not exists in data', $expression->getMethod()), 0, null);
    }
}