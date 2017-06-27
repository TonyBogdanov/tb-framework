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
 * Editor asset manager.
 *
 * Class Editor
 * @package TB\Asset
 */
class Editor extends Asset implements InitializableInterface
{
    /**
     * @return $this
     */
    public function __actionAddEditorStyles()
    {
        foreach ($this->getStyles() as $style) {
            add_editor_style($style['url']);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        add_action('admin_init', array($this, '__actionAddEditorStyles'));
    }

    /**
     * @inheritDoc
     */
    public function enqueueScript($name, $url = '', array $dependencies = [], $version = null, $inFooter = false)
    {
        throw new \Exception(__CLASS__ . ' does not support enqueueing scripts.');
    }

    /**
     * @inheritDoc
     */
    public function enqueueImport($url)
    {
        throw new \Exception(__CLASS__ . ' does not support enqueueing import.');
    }

    /**
     * @inheritDoc
     */
    public function enqueueMedia()
    {
        throw new \Exception(__CLASS__ . ' does not support enqueueing media.');
    }
}