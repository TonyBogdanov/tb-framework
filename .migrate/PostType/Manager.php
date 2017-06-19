<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Custom post type manager.
 *
 * Class TB_PostType_Manager
 */
class TB_PostType_Manager extends TB_Trait_AppAware_Constructor
{
    /**
     * @var array
     */
    protected $postTypes = array();

    /**
     * @var bool
     */
    protected $inited = false;

    /**
     * Initialize manager.
     *
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
     * Register a custom post type.
     * Can be called multiple times.
     *
     * @param $name
     * @param array $options
     *
     * @return $this
     */
    public function register($name, array $options)
    {
        $this->init();
        $this->postTypes[$name] = $options;
        return $this;
    }

    /**
     * @return $this
     */
    public function hookInit()
    {
        $enableVisualComposer = array();

        foreach($this->postTypes as $name => $options) {
            register_post_type($name, $options);
            if(isset($options['visual_composer']) && $options['visual_composer']) {
                $enableVisualComposer[] = $name;
            }
        }

        if(0 < count($enableVisualComposer) && $this->getApp()->plugin()->isActive('js_composer')) {
            vc_set_default_editor_post_types(array_unique(array_merge(vc_editor_post_types(), $enableVisualComposer)));
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
}