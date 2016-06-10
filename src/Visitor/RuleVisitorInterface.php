<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 6/10/16
 * Time: 12:07 PM
 */

namespace Vain\Rule\Visitor;

use Vain\Expression\Visitor\VisitorInterface;
use Vain\Rule\Result\RuleResult;
use Vain\Rule\RuleInterface;

interface RuleVisitorInterface extends VisitorInterface
{
    /**
     * @param RuleInterface $rule
     *
     * @return RuleResult
     */
    public function rule(RuleInterface $rule);
}