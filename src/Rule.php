<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 6/10/16
 * Time: 9:48 AM
 */

namespace Vain\Rule;

use Vain\Expression\ExpressionInterface;
use Vain\Expression\Serializer\SerializerInterface;

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
    public function unserialize(SerializerInterface $serializer, array $serializedData)
    {
        list ($this->name, $expressionData) = $serializedData;
        $this->expression = $serializer->unserializeExpression($expressionData);

        return $this;
    }
}