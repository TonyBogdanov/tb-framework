<?php

/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Filter_Attachment
 */
class TB_Form_Filter_Attachment extends TB_Form_Filter_Post
{
    /**
     * @inheritDoc
     */
    public function filter($value, TB_Form_Element $element)
    {
        $post = parent::filter($value, $element);

        // parent would have thrown an exception if not a post and flag is raised
        // so only case left is, flag is not raised
        if(!$post instanceof WP_Post) {
            return $value;
        }

        if('attachment' != $post->post_type) {
            if($this->ownConfig()->has(self::CONFIG_THROW_NOT_FOUND) && $this->ownConfig(self::CONFIG_THROW_NOT_FOUND)) {
                throw new Exception('Post is not of type attachment');
            }
        }

        return $post;
    }
}