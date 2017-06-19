<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Filter_ArraySlice
 */
class TB_Form_Filter_ArraySlice extends TB_Form_Filter
{
    /**
     * Slice offset.
     * {int}
     */
    const CONFIG_OFFSET = 'offset';

    /**
     * Slice length.
     * {int}
     */
    const CONFIG_LENGTH = 'length';

    /**
     * @inheritDoc
     */
    public function filter($value, TB_Form_Element $element)
    {
        if(!is_array($value)) {
            return $value;
        }

        $offset = $this->ownConfig()->has(self::CONFIG_OFFSET) ? $this->ownConfig(self::CONFIG_OFFSET) : 0;
        $length = $this->ownConfig()->has(self::CONFIG_LENGTH) ? $this->ownConfig(self::CONFIG_LENGTH) : PHP_INT_MAX;

        return array_slice($value, $offset, $length);
    }
}