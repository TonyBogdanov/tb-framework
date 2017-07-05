<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Utils;

/**
 * Adds plugin utilities awareness.
 *
 * Trait PluginAwareTrait
 * @package TB\Utils
 */
trait PluginAwareTrait
{
    /**
     * @return Plugin
     */
    public function getPluginUtils()
    {
        return $this->get('tb.utils.plugin');
    }
}