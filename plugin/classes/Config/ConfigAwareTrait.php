<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Config;

use TB\Config\Config;

/**
 * Adds own instance of a configuration holder.
 *
 * Trait ConfigAwareTrait
 * @package TB\ServiceManager
 */
trait ConfigAwareTrait
{
    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->get('tb.config.config');
    }
}