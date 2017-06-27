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
 * Frontend asset manager.
 *
 * Class Frontend
 * @package TB\Asset
 */
class Frontend extends Asset implements InitializableInterface
{
    /**
     * @inheritDoc
     */
    public function initialize()
    {
        add_action('wp_enqueue_scripts', array($this, '__actionEnqueueScripts'));
        add_action('wp_head', array($this, '__actionEnqueueImports'));
    }
}