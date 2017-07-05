<?php

/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Filter_String
 */
class TB_Form_Filter_String extends TB_Form_Filter
{
    /**
     * @inheritDoc
     */
    public function filter($value, TB_Form_Element $element)
    {
        /** @var TB_Util_String $utilString */
        $utilString = $this->sm('util.string');

        if(!$utilString->canBeCastToString($value)) {
            return $value;
        }

        return (string) $value;
    }
}