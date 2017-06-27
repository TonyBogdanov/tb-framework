<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Asset;

/**
 * Adds backend asset manager awareness.
 *
 * Trait BackendAwareTrait
 * @package TB\Asset
 */
trait BackendAwareTrait
{
    /**
     * @return Backend
     */
    public function getBackendAsset()
    {
        return $this->get('tb.asset.backend');
    }
}