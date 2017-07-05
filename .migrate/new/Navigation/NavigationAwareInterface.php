<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Navigation;

/**
 * Adds navigation awareness.
 *
 * Interface NavigationAwareInterface
 * @package TB\Navigation
 */
interface NavigationAwareInterface
{
    /**
     * @return Navigation
     */
    public function getNavigation();
}