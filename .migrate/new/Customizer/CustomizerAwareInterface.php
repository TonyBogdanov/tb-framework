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
 * Interface CustomizerAwareInterface
 * @package TB\Customizer
 */
interface CustomizerAwareInterface
{
    /**
     * @return Customizer
     */
    public function getCustomizer();
}