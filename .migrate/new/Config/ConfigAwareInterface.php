<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Config;

/**
 * Adds own instance of a configuration holder.
 *
 * Interface ConfigAwareInterface
 * @package TB\Config
 */
interface ConfigAwareInterface
{
    /**
     * @return Config
     */
    public function getConfig();
}