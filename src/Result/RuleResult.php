<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 6/10/16
 * Time: 9:41 AM
 */

namespace Vain\Rule\Result;

use Vain\Core\Result\AbstractResult;
use Vain\Expression\Result\ResultExpressionInterface;
use Vain\Expression\Visitor\VisitorInterface;
use Vain\Rule\RuleInterface;

class RuleResult extends AbstractResult implements ResultExpressionInterface
{
    private $rule;

    private $result;

    /**
     * RuleResult constructor.
     * @param RuleInterface $rule
     * @param ResultExpressionInterface $resultExpression
     */
    public function __construct(RuleInterface $rule, ResultExpressionInterface $resultExpression)
    {
        $this->rule = $rule;
        $this->result = $resultExpression;
        parent::__construct($resultExpression->getStatus());
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor)
    {
        return $this->result->accept($visitor);
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return json_encode(['rule' => serialize($this->rule), 'result' => serialize($this->result)]);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $serializedData = json_decode($serialized);
        $this->rule = unserialize($serializedData->rule);
        $this->result = unserialize($serializedData->result);

        return $this;
    }
}