<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 6/10/16
 * Time: 9:41 AM
 */

namespace Vain\Rule\Result;

use Vain\Expression\Boolean\Result\BooleanResultInterface;
use Vain\Rule\RuleInterface;

class RuleResult implements BooleanResultInterface
{
    private $rule;

    private $result;

    /**
     * RuleResult constructor.
     * @param RuleInterface $rule
     * @param BooleanResultInterface $result
     */
    public function __construct(RuleInterface $rule, BooleanResultInterface $result)
    {
        $this->rule = $rule;
        $this->result = $result;
    }

    /**
     * @inheritDoc
     */
    public function isSuccessful()
    {
        return $this->result->isSuccessful();
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->result->getStatus();
    }

    /**
     * @inheritDoc
     */
    public function invert()
    {
        $copy = clone $this;
        $this->result = $copy->result->invert();

        return $copy;
    }

    /**
     * @inheritDoc
     */
    public function interpret(\ArrayAccess $context = null)
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return sprintf('%s: %s', $this->rule->getName(), $this->result);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return ['rule_result', ['rule' => $this->rule->toArray(), 'result' => $this->result->toArray()]];
    }


}