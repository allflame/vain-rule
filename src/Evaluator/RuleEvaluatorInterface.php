<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 6/13/16
 * Time: 12:10 PM
 */

namespace Vain\Rule\Evaluator;

use Vain\Rule\RuleInterface;

interface RuleEvaluatorInterface
{
    /**
     * @param RuleInterface $rule
     *
     * @return mixed
     */
    public function evaluate(RuleInterface $rule);
}