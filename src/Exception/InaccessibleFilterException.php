<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 4/7/16
 * Time: 11:43 AM
 */

namespace Vain\Rule\Exception;

use Vain\Expression\ExpressionInterface;
use Vain\Rule\Evaluator\EvaluatorInterface;

class InaccessibleFilterException extends ExpressionEvaluatorException
{
    private $value;

    /**
     * InaccessibleFilterException constructor.
     * @param EvaluatorInterface $evaluator
     * @param ExpressionInterface $expression
     * @param string $value
     */
    public function __construct(EvaluatorInterface $evaluator, ExpressionInterface $expression, $value)
    {
        $this->value = $value;
        parent::__construct($evaluator, $expression, sprintf('Cannot apply filter for non-traversable object'), 0, null);
    }

    /**
     * @return object
     */
    public function getValue()
    {
        return $this->value;
    }
}