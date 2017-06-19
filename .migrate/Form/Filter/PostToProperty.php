<?php

/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Filter_PostToProperty
 */
class TB_Form_Filter_PostToProperty extends TB_Form_Filter implements TB_Initializable
{
    /**
     * To which WP_Post property to convert.
     * {string}
     */
    const CONFIG_PROPERTY = 'property';

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->ownConfig()->depend(self::CONFIG_PROPERTY);
    }

    /**
     * @inheritDoc
     */
    public function filter($value, TB_Form_Element $element)
    {
        if(
            !$value instanceof WP_Post ||
            !property_exists($value, $this->ownConfig(self::CONFIG_PROPERTY))
        ) {
            return $value;
        }

        $property = $this->ownConfig(self::CONFIG_PROPERTY);
        return $value->$property;
    }
}