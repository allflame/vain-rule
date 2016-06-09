<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 4/5/16
 * Time: 6:29 PM
 */

namespace Vain\Rule\Exception;

use Vain\Expression\NonTerminal\Property\PropertyExpression;
use Vain\Rule\Evaluator\EvaluatorInterface;

class UnknownPropertyException extends ExpressionEvaluatorException
{
    /**
     * UnknownPropertyException constructor.
     * @param EvaluatorInterface $evaluator
     * @param PropertyExpression $expression
     */
    public function __construct(EvaluatorInterface $evaluator, PropertyExpression $expression)
    {
        parent::__construct($evaluator, $expression, sprintf('Property %s not found in data', $expression->getProperty()), 0, null);
    }
}