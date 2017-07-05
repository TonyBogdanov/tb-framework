<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Asset
 *
 * UI visibility control service.
 */
class TB_Visibility extends TB_ServiceManager_Aware implements TB_Initializable
{
    /**
     * @inheritDoc
     */
    public function init()
    {
        /** @var TB_Asset_Backend $assetBackend */
        $assetBackend = $this->sm('asset.backend');

        /** @var TB_Uri $uri */
        $uri = $this->sm('uri');

        // enqueue script
        $assetBackend->enqueueScript(
            'tb-backend-visibility',
            $uri->framework('assets/scripts/backend-visibility.min.js'),
            array('jquery')
        );
    }

    /**
     * Register the specified element as a visibility scope.
     * Any visibility expressions found on elements in it's structure will use it as their scope.
     *
     * @param TB_DOM_Tag $tag
     * @return $this
     */
    public function registerScope(TB_DOM_Tag $tag)
    {
        $tag->setAttribute('data-visibility-scope');
        return $this;
    }
}