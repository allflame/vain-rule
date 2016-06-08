<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 4/4/16
 * Time: 11:19 PM
 */

namespace Vain\Rule\Exception;

use Vain\Expression\Unary\Mode\ModeExpression;
use Vain\Rule\Evaluator\EvaluatorInterface;

class ModeMismatchException extends ExpressionEvaluatorException
{
    private $mode;

    /**
     * ModeMismatchException constructor.
     * @param EvaluatorInterface $evaluator
     * @param ModeExpression $expression
     * @param string $mode
     */
    public function __construct(EvaluatorInterface $evaluator, ModeExpression $expression, $mode)
    {
        $this->mode = $mode;
        parent::__construct($evaluator, $expression, sprintf('Unable to compare values with different modes %s and %s', $expression->getMode(), $mode), 0, null);
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }
}