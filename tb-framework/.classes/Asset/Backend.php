<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Asset;

use TB\Initializable\InitializableInterface;

/**
 * Backend asset manager.
 *
 * Class Backend
 * @package TB\Asset
 */
class Backend extends Asset implements InitializableInterface
{
    /**
     * @inheritDoc
     */
    public function initialize()
    {
        add_action('admin_enqueue_scripts', array($this, '__actionEnqueueScripts'));
        add_action('admin_head', array($this, '__actionEnqueueImports'));
    }
}