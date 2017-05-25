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
 * Development utilities.
 *
 * Class Development
 * @package TB\Utils
 */
class Development implements ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

    /**
     * Flag to determine if the app is running in dev mode.
     *
     * @var bool
     */
    protected $devMode = false;

    /**
     * @return bool
     */
    public function isDevMode()
    {
        return $this->devMode;
    }

    /**
     * @param bool $devMode
     * @return Development
     */
    public function setDevMode($devMode)
    {
        $this->devMode = $devMode;
        return $this;
    }
}