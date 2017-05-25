<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Initializable;

/**
 * Mark an object as initializable.
 * The initialize() method must be called immediately after the object is constructed.
 *
 * Interface InitializableInterface
 * @package TB\Initializable
 */
interface InitializableInterface
{
    /**
     * @return $this
     */
    public function initialize();
}