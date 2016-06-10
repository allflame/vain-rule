<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 6/10/16
 * Time: 9:48 AM
 */

namespace Vain\Rule;

use Vain\Expression\ExpressionInterface;
use Vain\Expression\Visitor\VisitorInterface;
use Vain\Rule\Visitor\RuleVisitorInterface;

class Rule implements RuleInterface
{
    private $name;

    private $expression;

    /**
     * Rule constructor.
     * @param $name
     * @param ExpressionInterface $expression
     */
    public function __construct($name, ExpressionInterface $expression)
    {
        $this->name = $name;
        $this->expression = $expression;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ExpressionInterface
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @param RuleVisitorInterface $visitor
     */
    public function accept(VisitorInterface $visitor)
    {
        return $visitor->rule($this);
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return json_encode(['name' => $this->name, 'expression' => serialize($this->expression)]);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $serializedData = json_decode($serialized);
        $this->name = $serializedData->name;
        $this->expression = unserialize($serializedData->expression);

        return $this;
    }
}