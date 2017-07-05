<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Framework;

use TB\Framework\Bundle\AbstractBundle;

class Bundle extends AbstractBundle
{
    /**
     * @inheritDoc
     */
    protected function getBaseUrl()
    {
        return plugin_dir_url(realpath(__DIR__ . '/../tb-framework.php'));
    }

    /**
     * @inheritDoc
     */
    protected function getBasePath()
    {
        return realpath(__DIR__ . '/..');
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return [
            __DIR__ . '/../config/services.yml'
        ];
    }
}