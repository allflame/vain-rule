<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 4/7/16
 * Time: 10:36 AM
 */

namespace Vain\Rule\Exception;

use Vain\Expression\ExpressionInterface;
use Vain\Rule\Evaluator\EvaluatorInterface;

class InaccessiblePropertyException extends ExpressionEvaluatorException
{

    private $value;

    /**
     * InaccessiblePropertyException constructor.
     * @param EvaluatorInterface $evaluator
     * @param ExpressionInterface $expression
     * @param string $value
     */
    public function __construct(EvaluatorInterface $evaluator, ExpressionInterface $expression, $value)
    {
        $this->value = $value;
        parent::__construct($evaluator, $expression, sprintf('Cannot get property for unsupported value type %s', gettype($value)), 0, null);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}