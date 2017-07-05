<?php

/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Filter_Post
 */
class TB_Form_Filter_Post extends TB_Form_Filter
{
    /**
     * Should throw an exception if post not found?
     * {bool}
     */
    const CONFIG_THROW_NOT_FOUND = 'throw_not_found';

    /**
     * @inheritDoc
     */
    public function filter($value, TB_Form_Element $element)
    {
        if($value instanceof WP_Post) {
            return $value;
        }

        /** @var TB_Util_String $utilString */
        $utilString = $this->sm('util.string');
        if(!$utilString->canBeCastToString($value)) {
            return $value;
        }

        $post = get_post((string) $value);

        if(!$post) {
            if($this->ownConfig()->has(self::CONFIG_THROW_NOT_FOUND) && $this->ownConfig(self::CONFIG_THROW_NOT_FOUND)) {
                throw new Exception('Post with the supplied ID not found: ' . $value);
            }
            return $value;
        }

        return $post;
    }
}