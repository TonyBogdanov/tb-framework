<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Asset;

/**
 * Adds editor asset manager awareness.
 *
 * Interface EditorAwareInterface
 * @package TB\Asset
 */
interface EditorAwareInterface
{
    /**
     * @return Editor
     */
    public function getEditorAsset();
}