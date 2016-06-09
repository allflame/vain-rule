<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 4/1/16
 * Time: 10:26 AM
 */

namespace Vain\Rule\Evaluator\Decorator;

use Vain\Rule\Evaluator\EvaluatorInterface;
use Visitor\Decorator\AbstractVisitorDecorator;

/**
 * @method EvaluatorInterface getVisitor
 */
abstract class AbstractEvaluatorDecorator extends AbstractVisitorDecorator implements EvaluatorInterface
{
    /**
     * AbstractVainExpressionDecorator constructor.
     * @param EvaluatorInterface $evaluator
     */
    public function __construct(EvaluatorInterface $evaluator)
    {
        parent::__construct($evaluator);
    }

    public function withContext(\ArrayAccess $context = null)
    {
        return $this->getVisitor()->withContext($context);
    }
} 