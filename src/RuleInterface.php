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

namespace Vain\Rule;

use Vain\Expression\Boolean\BooleanExpressionInterface;
use Vain\Expression\ExpressionInterface;

/**
 * Interface RuleInterface
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
interface RuleInterface extends BooleanExpressionInterface
{
    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @return ExpressionInterface
     */
    public function getExpression() : ExpressionInterface;
}