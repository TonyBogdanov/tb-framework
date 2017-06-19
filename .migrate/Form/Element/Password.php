<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Element_Password
 */
class TB_Form_Element_Password extends TB_Form_Element_Text
{
    /**
     * @inheritDoc
     */
    public function render(array $parents = array())
    {
        // carrier
        $element = parent::render($parents);
        $carrier = 'input' === $element->getTag() ? $element : $element->findOne('input');
        $carrier->setAttribute('type', 'password');
        return $this->decorate(__CLASS__, $element, $parents);
    }
}