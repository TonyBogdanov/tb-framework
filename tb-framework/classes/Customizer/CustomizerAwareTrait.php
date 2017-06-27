<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Customizer;

/**
 * Adds customizer awareness.
 *
 * Trait CustomizerAwareTrait
 * @package TB\Customizer
 */
trait CustomizerAwareTrait
{
    /**
     * @return Customizer
     */
    public function getCustomizer()
    {
        return $this->get('tb.customizer.customizer');
    }
}