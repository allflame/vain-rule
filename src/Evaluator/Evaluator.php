<?php
/**
 * Created by PhpStorm.
 * User: allflame
 * Date: 4/4/16
 * Time: 10:07 PM
 */

namespace Vain\Rule\Evaluator;

use Vain\Comparator\Repository\ComparatorRepositoryInterface;
use Vain\Comparator\Result\ComparableResult;
use Vain\Data\Module\Repository\ModuleRepositoryInterface;
use Vain\Expression\Boolean\AndX\AndExpression;
use Vain\Expression\Boolean\Equal\EqualExpression;
use Vain\Expression\Boolean\Greater\GreaterExpression;
use Vain\Expression\Boolean\GreaterOrEqual\GreaterOrEqualExpression;
use Vain\Expression\Boolean\In\InExpression;
use Vain\Expression\Boolean\Less\LessExpression;
use Vain\Expression\Boolean\LessOrEqual\LessOrEqualExpression;
use Vain\Expression\Boolean\Like\LikeExpression;
use Vain\Expression\Boolean\NotEqual\NotEqualExpression;
use Vain\Expression\Boolean\OrX\OrExpression;
use Vain\Expression\Boolean\False\FalseExpression;
use Vain\Expression\Boolean\True\TrueExpression;
use Vain\Expression\Boolean\Identity\IdentityExpression;
use Vain\Expression\Boolean\Not\NotExpression;
use Vain\Expression\Result\ResultExpressionInterface;
use Vain\Expression\Terminal\Context\ContextExpression;
use Vain\Expression\Terminal\InPlace\InPlaceExpression;
use Vain\Expression\NonTerminal\Module\ModuleExpression;
use Vain\Expression\NonTerminal\Filter\FilterExpression;
use Vain\Expression\NonTerminal\FunctionX\FunctionExpression;
use Vain\Expression\NonTerminal\Helper\HelperExpression;
use Vain\Expression\NonTerminal\Method\MethodExpression;
use Vain\Expression\NonTerminal\Mode\ModeExpression;
use Vain\Expression\NonTerminal\Property\PropertyExpression;
use Vain\Rule\Exception\InaccessibleFilterException;
use Vain\Rule\Exception\InaccessiblePropertyException;
use Vain\Rule\Exception\UnknownFunctionException;
use Vain\Rule\Exception\UnknownHelperException;
use Vain\Rule\Exception\UnknownMethodException;
use Vain\Rule\Exception\UnknownPropertyException;
use Vain\Rule\Result\RuleResult;

class Evaluator implements EvaluatorInterface
{

    private $moduleRepository;

    private $comparatorRepository;

    private $context;

    /**
     * ExpressionEvaluator constructor.
     * @param ModuleRepositoryInterface $moduleRepository
     * @param ComparatorRepositoryInterface $comparatorRepository
     * @param \ArrayAccess $context
     */
    public function __construct(ModuleRepositoryInterface $moduleRepository, ComparatorRepositoryInterface $comparatorRepository, \ArrayAccess $context = null)
    {
        $this->moduleRepository = $moduleRepository;
        $this->comparatorRepository = $comparatorRepository;
        $this->context = $context;
    }

    /**
     * @inheritDoc
     */
    public function result(ResultExpressionInterface $resultExpression)
    {
        return $resultExpression->getStatus();
    }

    /**
     * @inheritDoc
     */
    public function inPlace(InPlaceExpression $inPlaceExpression)
    {
        return $inPlaceExpression->getValue();
    }

    /**
     * @inheritDoc
     */
    public function module(ModuleExpression $moduleExpression)
    {
        return $this->moduleRepository->getModule($moduleExpression->getExpression()->accept($this))->getData($this->context);
    }

    /**
     * @inheritDoc
     */
    public function context(ContextExpression $contextExpression)
    {
        return $this->context;
    }

    /**
     * @inheritDoc
     */
    public function method(MethodExpression $methodExpression)
    {
        $data = $methodExpression->getExpression()->accept($this);
        $method = $methodExpression->getMethod();

        if (false === method_exists($data, $method)) {
            throw new UnknownMethodException($this, $methodExpression);
        }

        return call_user_func([$data, $method], ...$methodExpression->getArguments());
    }

    /**
     * @inheritDoc
     */
    public function property(PropertyExpression $propertyExpression)
    {
        $data = $propertyExpression->getExpression()->accept($this);
        $property = $propertyExpression->getProperty();

        switch(true) {
            case is_array($data):
                if (false === array_key_exists($property, $data)) {
                    throw new UnknownPropertyException($this, $propertyExpression);
                }
                return $data[$property];
                break;
            case $data instanceof \ArrayAccess:
                if (false === $data->offsetExists($property)) {
                    throw new UnknownPropertyException($this, $propertyExpression);
                }
                return $data->offsetGet($property);
                break;
            case is_object($data):
                return $data->{$property};
                break;
            default:
                throw new InaccessiblePropertyException($this, $propertyExpression, $data);
                break;
        }
    }

    /**
     * @inheritDoc
     */
    public function functionX(FunctionExpression $functionExpression)
    {
        $function = $functionExpression->getFunctionName();

        if (false === function_exists($function)) {
            throw new UnknownFunctionException($this, $functionExpression);
        }

        return call_user_func($function, $functionExpression->getExpression()->accept($this), ...$functionExpression->getArguments());
    }

    /**
     * @inheritDoc
     */
    public function mode(ModeExpression $modeExpression)
    {
        $value = $modeExpression->getExpression()->accept($this);

        switch ($modeExpression->getMode()) {
            case 'int':
                return (int)$value;
                break;
            case 'string':
                return (string)$value;
                break;
            case 'float':
            case 'double':
                return (float)$value;
                break;
            case 'bool':
            case 'boolean':
                return (bool)$value;
                break;
            default:
                return $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function filter(FilterExpression $filterExpression)
    {
        $dataToFilter = $filterExpression->getExpression()->accept($this);

        if (false === is_array($dataToFilter) && false === $dataToFilter instanceof \Traversable) {
            throw new InaccessibleFilterException($this, $filterExpression->getExpression(), $dataToFilter);
        }

        $filteredData = [];
        foreach ($dataToFilter as $singleElement) {
            if (false === $filterExpression->getFilterExpression()->accept($this->withContext($singleElement))->getStatus()) {
                continue;
            }
            $filteredData[] = $singleElement;
        }

        return $filteredData;
    }

    /**
     * @inheritDoc
     */
    public function helper(HelperExpression $helperExpression)
    {
        $data = $helperExpression->getExpression()->accept($this);
        $class = $helperExpression->getClass();
        $method = $helperExpression->getMethod();

        if (false === method_exists($class, $method)) {
            throw new UnknownHelperException($this, $helperExpression);
        }

        return call_user_func([$class, $method], $data, ...$helperExpression->getArguments());
    }

    /**
     * @inheritDoc
     */
    public function id(IdentityExpression $unaryExpression)
    {
        return $unaryExpression->getExpression()->accept($this);
    }

    /**
     * @inheritDoc
     */
    public function not(NotExpression $unaryExpression)
    {
        return $unaryExpression->getExpression()->accept($this)->invert();
    }

    /**
     * @inheritDoc
     */
    public function eq(EqualExpression $comparisonExpression)
    {
        $whatValue = $comparisonExpression->getFirstExpression()->accept($this);
        $againstValue = $comparisonExpression->getSecondExpression()->accept($this);

        return $this->comparatorRepository->getComparator(gettype($whatValue))->eq($whatValue, $againstValue);
    }

    /**
     * @inheritDoc
     */
    public function neq(NotEqualExpression $comparisonExpression)
    {
        $whatValue = $comparisonExpression->getFirstExpression()->accept($this);
        $againstValue = $comparisonExpression->getSecondExpression()->accept($this);

        return $this->comparatorRepository->getComparator(gettype($whatValue))->neq($whatValue, $againstValue);
    }

    /**
     * @inheritDoc
     */
    public function gt(GreaterExpression $comparisonExpression)
    {
        $whatValue = $comparisonExpression->getFirstExpression()->accept($this);
        $againstValue = $comparisonExpression->getSecondExpression()->accept($this);

        return $this->comparatorRepository->getComparator(gettype($whatValue))->gt($whatValue, $againstValue);
    }

    /**
     * @inheritDoc
     */
    public function gte(GreaterOrEqualExpression $comparisonExpression)
    {
        $whatValue = $comparisonExpression->getFirstExpression()->accept($this);
        $againstValue = $comparisonExpression->getSecondExpression()->accept($this);

        return $this->comparatorRepository->getComparator(gettype($whatValue))->gte($whatValue, $againstValue);
    }

    /**
     * @inheritDoc
     */
    public function lt(LessExpression $comparisonExpression)
    {
        $whatValue = $comparisonExpression->getFirstExpression()->accept($this);
        $againstValue = $comparisonExpression->getSecondExpression()->accept($this);

        return $this->comparatorRepository->getComparator(gettype($whatValue))->lt($whatValue, $againstValue);
    }

    /**
     * @inheritDoc
     */
    public function lte(LessOrEqualExpression $comparisonExpression)
    {
        $whatValue = $comparisonExpression->getFirstExpression()->accept($this);
        $againstValue = $comparisonExpression->getSecondExpression()->accept($this);

        return $this->comparatorRepository->getComparator(gettype($whatValue))->lte($whatValue, $againstValue);
    }

    /**
     * @inheritDoc
     */
    public function in(InExpression $comparisonExpression)
    {
        $whatValue = $comparisonExpression->getFirstExpression()->accept($this);
        $againstValue = $comparisonExpression->getSecondExpression()->accept($this);

        return $this->comparatorRepository->getComparator(gettype($whatValue))->in($whatValue, $againstValue);
    }

    /**
     * @inheritDoc
     */
    public function like(LikeExpression $comparisonExpression)
    {
        $whatValue = $comparisonExpression->getFirstExpression()->accept($this);
        $againstValue = $comparisonExpression->getSecondExpression()->accept($this);

        return $this->comparatorRepository->getComparator(gettype($whatValue))->like($whatValue, $againstValue);
    }

    /**
     * @inheritDoc
     */
    public function andX(AndExpression $binaryExpression)
    {
        $firstResult = $binaryExpression->getFirstExpression()->accept($this);
        $secondResult = $binaryExpression->getFirstExpression()->accept($this);

        return new RuleResult( new AndExpression($firstResult, $secondResult));
    }

    /**
     * @inheritDoc
     */
    public function orX(OrExpression $binaryExpression)
    {
        $firstResult = $binaryExpression->getFirstExpression()->accept($this);
        $secondResult = $binaryExpression->getFirstExpression()->accept($this);

        return new CompositeResult($firstResult->getStatus() || $secondResult->getStatus(), new AndExpression($firstResult, $secondResult));
    }

    /**
     * @inheritDoc
     */
    public function true(TrueExpression $expression)
    {
        return new ComparableResult(true);
    }

    /**
     * @inheritDoc
     */
    public function false(FalseExpression $expression)
    {
        return new ComparableResult(false);
    }

    /**
     * @inheritDoc
     */
    public function withContext(\ArrayAccess $context = null)
    {
        $clone = clone $this;
        $clone->context = $context;

        return $clone;
    }
}