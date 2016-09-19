<?php
/**
 * Vain Framework
 *
 * PHP Version 7
 *
 * @package   vain-expression
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://github.com/allflame/vain-expression
 */
declare(strict_types = 1);

namespace Vain\Rule\Result;

use Vain\Core\Result\ResultInterface;
use Vain\Expression\Boolean\Result\BooleanResultInterface;
use Vain\Rule\RuleInterface;

/**
 * Class RuleResult
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class RuleResult implements BooleanResultInterface
{
    private $rule;

    private $result;

    /**
     * RuleResult constructor.
     *
     * @param RuleInterface          $rule
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
    public function isSuccessful() : bool
    {
        return $this->result->isSuccessful();
    }

    /**
     * @inheritDoc
     */
    public function getStatus() : bool
    {
        return $this->result->getStatus();
    }

    /**
     * @inheritDoc
     */
    public function invert() : ResultInterface
    {
        $copy = clone $this;
        $this->result = $copy->result->invert();

        return $copy;
    }

    /**
     * @inheritDoc
     */
    public function interpret(\ArrayAccess $context = null) : ResultInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toString() : string
    {
        return sprintf('%s: %s', $this->rule->getName(), $this->result);
    }

    /**
     * @inheritDoc
     */
    public function toArray() : array
    {
        return ['rule_result' => ['rule' => $this->rule->toArray(), 'result' => $this->result->toArray()]];
    }
}