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
 * Trait NavigationAwareTrait
 * @package TB\Navigation
 */
trait NavigationAwareTrait
{
    /**
     * @return Uri
     */
    public function getNavigation()
    {
        return $this->get('tb.navigation.navigation');
    }
}