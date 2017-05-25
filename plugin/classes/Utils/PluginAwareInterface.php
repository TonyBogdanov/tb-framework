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
 * Interface PluginAwareInterface
 * @package TB\Utils
 */
interface PluginAwareInterface
{
    /**
     * @return Plugin
     */
    public function getPluginUtils();
}