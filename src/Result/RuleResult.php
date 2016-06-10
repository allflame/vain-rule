<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 6/10/16
 * Time: 9:41 AM
 */

namespace Vain\Rule\Result;

use Vain\Comparator\Result\ComparableResultInterface;
use Vain\Core\Result\AbstractResult;
use Vain\Core\Result\ResultInterface;
use Vain\Expression\ExpressionInterface;
use Vain\Expression\Serializer\SerializerInterface;
use Vain\Expression\Visitor\VisitorInterface;

class RuleResult extends AbstractResult implements ResultInterface, ExpressionInterface
{
    private $comparableResult;

    private $expression;

    /**
     * RuleResult constructor.
     * @param ComparableResultInterface $comparableResult
     * @param ExpressionInterface $expression
     */
    public function __construct(ComparableResultInterface $comparableResult, ExpressionInterface $expression)
    {
        $this->comparableResult = $comparableResult;
        $this->expression = $expression;
        parent::__construct($comparableResult->getStatus());
    }

    /**
     * @return ComparableResultInterface
     */
    public function getComparableResult()
    {
        return $this->comparableResult;
    }

    /**
     * @return ExpressionInterface
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor)
    {
        return $visitor->result($this->comparableResult);
    }

    /**
     * @inheritDoc
     */
    public function unserialize(SerializerInterface $serializer, array $serializedData)
    {
        list ($comparableResultData, $expressionData) = $serializedData;
        $this->comparableResult = $serializer->unserializeExpression($comparableResultData);
        $this->expression = $serializer->unserializeExpression($expressionData);

        return $this;
    }
}