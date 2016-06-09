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
use Vain\Expression\Binary\AndX\AndExpression;
use Vain\Expression\Binary\Equal\EqualExpression;
use Vain\Expression\Binary\Greater\GreaterExpression;
use Vain\Expression\Binary\GreaterOrEqual\GreaterOrEqualExpression;
use Vain\Expression\Binary\In\InExpression;
use Vain\Expression\Binary\Less\LessExpression;
use Vain\Expression\Binary\LessOrEqual\LessOrEqualExpression;
use Vain\Expression\Binary\Like\LikeExpression;
use Vain\Expression\Binary\NotEqual\NotEqualExpression;
use Vain\Expression\Binary\OrX\OrExpression;
use Vain\Expression\False\FalseExpression;
use Vain\Expression\Terminal\InPlace\InPlaceExpression;
use Vain\Expression\Terminal\Local\LocalExpression;
use Vain\Expression\Terminal\Module\ModuleExpression;
use Vain\Expression\True\TrueExpression;
use Vain\Expression\Unary\Filter\FilterExpression;
use Vain\Expression\Unary\FunctionX\FunctionExpression;
use Vain\Expression\Unary\Helper\HelperExpression;
use Vain\Expression\Unary\Identity\IdentityExpression;
use Vain\Expression\Unary\Method\MethodExpression;
use Vain\Expression\Unary\Mode\ModeExpression;
use Vain\Expression\Unary\Not\NotExpression;
use Vain\Expression\Unary\Property\PropertyExpression;
use Vain\Rule\Exception\InaccessibleFilterException;
use Vain\Rule\Exception\InaccessiblePropertyException;
use Vain\Rule\Exception\UnknownFunctionException;
use Vain\Rule\Exception\UnknownHelperException;
use Vain\Rule\Exception\UnknownMethodException;
use Vain\Rule\Exception\UnknownPropertyException;

class Evaluator implements EvaluatorInterface
{

    private $comparatorRepository;

    private $context;

    /**
     * ExpressionEvaluator constructor.
     * @param ComparatorRepositoryInterface $comparatorRepository
     * @param \ArrayAccess $context
     */
    public function __construct(ComparatorRepositoryInterface $comparatorRepository, \ArrayAccess $context = null)
    {
        $this->comparatorRepository = $comparatorRepository;
        $this->context = $context;
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
        return $moduleExpression->getModule()->getData($this->context);
    }

    /**
     * @inheritDoc
     */
    public function local(LocalExpression $localExpression)
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
            if (false === $filterExpression->accept($this->withContext($singleElement))->getStatus()) {
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

        if (false === $firstResult->getStatus()) {
            return new ComparableResult(false);
        }

        if (false === $secondResult->getStatus()) {
            return new ComparableResult(false);
        }

        return new ComparableResult(true);
    }

    /**
     * @inheritDoc
     */
    public function orX(OrExpression $binaryExpression)
    {
        $firstResult = $binaryExpression->getFirstExpression()->accept($this);
        $secondResult = $binaryExpression->getFirstExpression()->accept($this);

        if (false === $firstResult->getStatus() && false === $secondResult->getStatus()) {
            return new ComparableResult(false);
        }

        return new ComparableResult(true);
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