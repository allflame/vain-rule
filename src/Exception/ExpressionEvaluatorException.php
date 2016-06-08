<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 4/4/16
 * Time: 11:18 PM
 */

namespace Vain\Rule\Exception;

use Vain\Core\Exception\CoreException;
use Vain\Rule\Evaluator\EvaluatorInterface;
use Vain\Expression\ExpressionInterface;

class ExpressionEvaluatorException extends CoreException
{
    private $evaluator;

    private $expression;

    /**
     * ExpressionEvaluatorException constructor.
     * @param EvaluatorInterface $evaluator
     * @param ExpressionInterface $expression
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct(EvaluatorInterface $evaluator, ExpressionInterface $expression, $message, $code, \Exception $previous = null)
    {
        $this->evaluator = $evaluator;
        $this->expression = $expression;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return EvaluatorInterface
     */
    public function getEvaluator()
    {
        return $this->evaluator;
    }

    /**
     * @return ExpressionInterface
     */
    public function getExpression()
    {
        return $this->expression;
    }
}