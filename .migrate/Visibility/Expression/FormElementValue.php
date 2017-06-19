<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Visibility_Expression_FormElementValue
 *
 * A form element value expression.
 */
class TB_Visibility_Expression_FormElementValue extends TB_Visibility_Expression
{
    /**
     * A form element name globally available and unique in the DOM.
     * If more than one element exists with that name, the first occurrence will be used.
     *
     * If a form element instance is passed instead, the form element name will be retrieved during getNotation().
     *
     * @var string|TB_Form_Element
     */
    protected $element;

    /**
     * TB_Visibility_Expression_FormElementValue constructor.
     *
     * @param string|TB_Form_Element $element
     */
    public function __construct($element)
    {
        $this->setElement($element);
    }

    /**
     * @inheritDoc
     */
    public function getNotation($context = null)
    {
        if($this->getElement() instanceof TB_Form_Element) {
            /** @var TB_Form_Element $element */
            $element = $this->getElement();

            // context is expected to be an array of form element parents
            // if it's not, check the decorator
            $value = $element->getNameForRendering($context);
        } else {
            $value = (string) $this->getElement();
        }

        return 'form_element_value:' . strlen($value) . ':' . $value;
    }

    /**
     * @return string|TB_Form_Element
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param string|TB_Form_Element $element
     * @return TB_Visibility_Expression_FormElementValue
     */
    public function setElement($element)
    {
        $this->element = $element;
        return $this;
    }
}