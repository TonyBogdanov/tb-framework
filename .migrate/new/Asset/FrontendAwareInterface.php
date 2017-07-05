<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Asset;

/**
 * Adds frontend asset manager awareness.
 *
 * Interface FrontendAwareInterface
 * @package TB\Asset
 */
interface FrontendAwareInterface
{
    /**
     * @return Frontend
     */
    public function getFrontendAsset();
}