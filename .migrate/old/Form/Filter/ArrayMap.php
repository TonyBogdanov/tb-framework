<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Filter_ArrayMap
 */
class TB_Form_Filter_ArrayMap extends TB_Form_Filter_Collection
{
    /**
     * Should the value (array) be mapped recursively.
     * {bool}
     */
    const CONFIG_RECURSIVE = 'recursive';

    /**
     * Should an array entry be removed from the array if any of the filters in the collection throw an exception?
     * {bool}
     */
    const CONFIG_REMOVE_ON_EXCEPTION = 'remove_on_exception';

    /**
     * @inheritDoc
     */
    public function filter($value, TB_Form_Element $element)
    {
        if(!is_array($value)) {
            return $value;
        }

        foreach($value as $key => $item) {
            if(
                is_array($item) &&
                $this->ownConfig()->has(self::CONFIG_RECURSIVE) &&
                $this->ownConfig(self::CONFIG_RECURSIVE)
            ) {
                $value[$key] = $this->filter($item, $element);
            } else {
                try {
                    $value[$key] = parent::filter($item, $element);
                } catch(Exception $e) {
                    if(
                        $this->ownConfig()->has(self::CONFIG_REMOVE_ON_EXCEPTION) &&
                        $this->ownConfig(self::CONFIG_REMOVE_ON_EXCEPTION)
                    ) {
                        unset($value[$key]);
                    } else {
                        throw $e;
                    }
                }
            }
        }

        return $value;
    }
}