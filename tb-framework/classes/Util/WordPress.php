<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Framework\Util;

class WordPress
{
    /**
     * Disable the WP Emoji feature.
     *
     * @return $this
     */
    public function disableEmoji()
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        return $this;
    }

    /**
     * Disable the WP EmbedJs feature.
     *
     * @return $this
     */
    public function disableEmbedJs()
    {
        add_action('wp_footer', function () {
            wp_dequeue_script('wp-embed');
        });
        return $this;
    }

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
        add_theme_support('post-thumbnails');
        return $this;
    }
}