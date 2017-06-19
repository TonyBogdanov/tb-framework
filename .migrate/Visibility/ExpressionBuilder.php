<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Visibility_Expression
 *
 * Expression builder for the visibility service.
 */
class TB_Visibility_ExpressionBuilder extends TB_ServiceManager_Aware
{
    /**
     * @param TB_Visibility_Expression $left
     * @param TB_Visibility_Expression $right
     * @return TB_Visibility_Expression_And
     */
    public function _and(TB_Visibility_Expression $left, TB_Visibility_Expression $right)
    {
        /** @var TB_Visibility_Expression_And $expression */
        $expression = $this->sm()->create('visibility.expression.and', array($left, $right));
        return $expression;
    }

    /**
     * @param TB_Visibility_Expression $left
     * @param TB_Visibility_Expression $right
     * @return TB_Visibility_Expression_Or
     */
    public function _or(TB_Visibility_Expression $left, TB_Visibility_Expression $right)
    {
        /** @var TB_Visibility_Expression_Or $expression */
        $expression = $this->sm()->create('visibility.expression.or', array($left, $right));
        return $expression;
    }

    /**
     * @param TB_Visibility_Expression $left
     * @param TB_Visibility_Expression $right
     * @return TB_Visibility_Expression_Equals
     */
    public function equals(TB_Visibility_Expression $left, TB_Visibility_Expression $right)
    {
        /** @var TB_Visibility_Expression_Equals $expression */
        $expression = $this->sm()->create('visibility.expression.equals', array($left, $right));
        return $expression;
    }

    /**
     * @param mixed $value
     * @return TB_Visibility_Expression_Value
     */
    public function value($value)
    {
        /** @var TB_Visibility_Expression_Value $expression */
        $expression = $this->sm()->create('visibility.expression.value', array($value));
        return $expression;
    }

    /**
     * @param string|TB_Form_Element $element
     * @return TB_Visibility_Expression_FormElementValue
     */
    public function formElementValue($element)
    {
        /** @var TB_Visibility_Expression_FormElementValue $expression */
        $expression = $this->sm()->create('visibility.expression.form_element_value', array($element));
        return $expression;
    }
}