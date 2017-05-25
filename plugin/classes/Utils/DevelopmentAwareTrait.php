<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Utils;

/**
 * Adds development utilities awareness.
 *
 * Trait DevelopmentAwareTrait
 * @package TB\Utils
 */
trait DevelopmentAwareTrait
{
    /**
     * @return Development
     */
    public function getDevelopmentUtils()
    {
        return $this->get('tb.utils.development');
    }
}