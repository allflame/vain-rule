<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 3/31/16
 * Time: 12:58 PM
 */

namespace Vain\Rule\Evaluator;

use Vain\Rule\Result\RuleResult;
use Vain\Rule\RuleInterface;
use Vain\Rule\Visitor\RuleVisitorInterface;

interface EvaluatorInterface extends RuleVisitorInterface
{
    /**
     * @param \ArrayAccess $context
     *
     * @return EvaluatorInterface
     */
    public function withContext(\ArrayAccess $context = null);

    /**
     * @param RuleInterface $rule
     *
     * @return RuleResult
     */
    public function rule(RuleInterface $rule);
}