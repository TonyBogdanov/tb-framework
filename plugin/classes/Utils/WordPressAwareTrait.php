<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Utils;

/**
 * Adds WordPress utilities awareness.
 *
 * Trait WordPressAwareTrait
 * @package TB\Utils
 */
trait WordPressAwareTrait
{
    /**
     * @return WordPress
     */
    public function getWordPressUtils()
    {
        return $this->get('tb.utils.word_press');
    }
}