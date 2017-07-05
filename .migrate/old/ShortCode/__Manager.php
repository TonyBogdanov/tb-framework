<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * ShortCode manager.
 *
 * Class TB_ShortCode_Manager
 */
class TB_ShortCode_Manager extends TB_Trait_AppAware_Constructor
{
    /**
     * @var array
     */
    protected $shortCodes = array();

    /**
     * @var bool
     */
    protected $inited = false;

    /**
     * @return $this
     */
    protected function init()
    {
        if($this->isInited()) {
            return $this;
        }
        add_action('init', array($this, 'hookInit'));
        $this->inited = true;
        return $this;
    }

    /**
     * Register ShortCodes, can be called multiple times.
     *
     * @param array $shortCodes
     *
     * @return $this
     */
    public function register(array $shortCodes)
    {
        $this->init();

        $vcEnabled = $this->getApp()->plugin()->isActive('js_composer');
        foreach($shortCodes as $className) {
            /** @var TB_ShortCode $shortCode */
            $shortCode = new $className($this);
            $this->shortCodes[$shortCode->getTag()] = $shortCode;

            if($shortCode instanceof TB_ShortCode_VisualComposerCompatible && $vcEnabled) {
                $this->getApp()->visualComposer()->register(array(
                    $shortCode->getTag() => $shortCode->getVisualComposerOptions()
                ));
            }
        }

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function hookInit()
    {
        if(!$this->isInited()) {
            throw new Exception('Manager is not initialized.');
        }
        foreach($this->shortCodes as $tag => $className) {
            add_shortcode($tag, array($this, 'invoke'));
        }
        return $this;
    }

    /**
     * Has the manager been initialized.
     *
     * @return bool
     */
    public function isInited()
    {
        return $this->inited;
    }

    /**
     * @param $args
     * @param $content
     * @param $tag
     *
     * @return string
     */
    public function invoke($args, $content, $tag)
    {
        /** @var TB_ShortCode $shortCode */
        $shortCode = $this->shortCodes[$tag];

        if(is_array($args)) {
            $shortCode->setArgs($args);
        }
        if(!empty($content)) {
            $shortCode->setContent($content);
        }

        return $shortCode->invoke();
    }
}