<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 5/10/16
 * Time: 11:38 AM
 */

namespace Vain\Rule\Exception;

use Vain\Expression\NonTerminal\Helper\HelperExpression;
use Vain\Rule\Evaluator\EvaluatorInterface;

class UnknownHelperException extends ExpressionEvaluatorException
{
    /**
     * UnknownHelperException constructor.
     * @param EvaluatorInterface $evaluator
     * @param HelperExpression $expression
     */
    public function __construct(EvaluatorInterface $evaluator, HelperExpression $expression)
    {
        parent::__construct($evaluator, $expression, sprintf('Helper method %s::%s is not registered', $expression->getClass(), $expression->getMethod()), 0, null);
    }
}