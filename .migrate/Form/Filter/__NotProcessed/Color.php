<?php

/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Filter_Color
 */
class TB_Form_Filter_Color extends TB_Form_Filter
{
    /**
     * @inheritDoc
     */
    public function filter($value, TB_Form_Element $element)
    {
        try {
            return new TB_Util_Color($value);
        } catch(Exception $e) {
            return $value;
        }
    }
}