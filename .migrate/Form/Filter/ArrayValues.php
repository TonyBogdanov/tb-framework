<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Filter_ArrayValues
 */
class TB_Form_Filter_ArrayValues extends TB_Form_Filter
{
    /**
     * @inheritDoc
     */
    public function filter($value, TB_Form_Element $element)
    {
        if(!is_array($value)) {
            return $value;
        }

        return array_values($value);
    }
}