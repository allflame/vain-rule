<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 3/31/16
 * Time: 12:58 PM
 */

namespace Vain\Rule\Evaluator;

use Vain\Expression\Visitor\VisitorInterface;

interface EvaluatorInterface extends VisitorInterface
{
    /**
     * @param \ArrayAccess $context
     *
     * @return EvaluatorInterface
     */
    public function withContext(\ArrayAccess $context = null);
}