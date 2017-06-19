<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\ServiceManager;

use TB\Config\Config;

/**
 * Adds awareness of the service manager's config.
 *
 * Interface ServiceManagerConfigAwareInterface
 * @package TB\ServiceManager
 */
interface ServiceManagerConfigAwareInterface
{
    /**
     * @return Config
     */
    public function getServiceManagerConfig();
}