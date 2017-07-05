<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Utils;

/**
 * WordPress theme utilities.
 *
 * Class Theme
 * @package TB\Utils
 */
class Theme extends WordPress
{
    /**
     * Adds HTML5 support for the current WordPress theme.
     *
     * @return $this
     */
    public function enableHtml5()
    {
        add_theme_support('html5');

        return $this;
    }

    /**
     * Adds title tag support for the current WordPress theme.
     *
     * @return $this
     */
    public function enableTitleTag()
    {
        add_theme_support('title-tag');

        return $this;
    }

    /**
     * Adds post thumbnails support for the current WordPress theme.
     *
     * @return $this
     */
    public function enablePostThumbnails()
    {
        add_theme_support('title-tag');

        return $this;
    }
}