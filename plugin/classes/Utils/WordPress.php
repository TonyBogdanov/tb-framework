<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Utils;

use TB\ServiceManager\ServiceManagerAwareInterface;
use TB\ServiceManager\ServiceManagerAwareTrait;

/**
 * WordPress utilities.
 *
 * Class WordPress
 * @package TB\Utils
 */
class WordPress implements ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

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
}