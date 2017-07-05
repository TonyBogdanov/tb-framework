<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Image manager.
 *
 * Class TB_Image_Manager
 */
class TB_Image_Manager extends TB_Trait_AppAware_Constructor
{
    /**
     * Creates a new transformable image.
     *
     * @param WP_Post $attachment
     *
     * @return TB_Image_Image
     * @throws Exception
     */
    public function open(WP_Post $attachment)
    {
        if('attachment' != $attachment->post_type) {
            throw new Exception('Could not open image, supplied post must be a valid attachment.');
        }

        list($type) = explode('/', $attachment->post_mime_type);
        if('image' != $type) {
            throw new Exception('Could not open image, supplied post must be a valid image, got: ' .
                                $attachment->post_mime_type);
        }

        return new TB_Image_Image(get_attached_file($attachment->ID));
    }
}