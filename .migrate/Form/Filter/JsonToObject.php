<?php

/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Filter_JsonToObject
 */
class TB_Form_Filter_JsonToObject extends TB_Form_Filter
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

        return json_decode((string) $value);
    }
}