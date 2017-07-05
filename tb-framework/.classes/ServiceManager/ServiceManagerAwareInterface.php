<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\ServiceManager;

use TB\ServiceManager\ServiceManager;

/**
 * Adds awareness of the service manager.
 *
 * Trait ServiceManagerAwareTrait
 * @package TB\ServiceManager
 */
interface ServiceManagerAwareInterface
{
    /**
     * @return ServiceManager
     */
    public function getServiceManager();

    /**
     * @param ServiceManager $serviceManager
     * @return ServiceManagerAwareTrait
     */
    public function setServiceManager(ServiceManager $serviceManager);
}