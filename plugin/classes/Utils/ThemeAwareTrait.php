<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Utils;

/**
 * Adds theme utilities awareness.
 *
 * Trait ThemeAwareTrait
 * @package TB\Utils
 */
trait ThemeAwareTrait
{
    /**
     * @return Theme
     */
    public function getThemeUtils()
    {
        return $this->get('tb.utils.theme');
    }
}