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
 * Trait ServiceManagerConfigAwareTrait
 * @package TB\ServiceManager
 */
trait ServiceManagerConfigAwareTrait
{
    /**
     * @return Config
     */
    public function getServiceManagerConfig()
    {
        /** @var Config $config */
        $config = $this->getServiceManager()->get('tb.config.config');
        return $config;
    }
}