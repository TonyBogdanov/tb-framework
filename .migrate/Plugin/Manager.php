<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Plugin manager.
 *
 * Class TB_Plugin_Manager
 */
class TB_Plugin_Manager extends TB_Trait_AppAware_Constructor
{
    /**
     * @var array
     */
    protected $plugins = array();

    /**
     * @var bool
     */
    protected $inited = false;

    /**
     * Ensure TGMPA is loaded and manager is registered with it.
     *
     * @return $this
     */
    protected function init()
    {
        if($this->isInited()) {
            return $this;
        }
        if(!function_exists('tgmpa')) {
            TB_Loader::load('TGM_Plugin_Activation');
        }
        add_action('tgmpa_register', array($this, 'hookTGMPA'));
        $this->inited = true;
        return $this;
    }

    /**
     * Register required / recommended plugins.
     * Can be called multiple times.
     *
     * @param array $plugins
     *
     * @return $this
     */
    public function register(array $plugins)
    {
        $this->init();
        $this->plugins = array_merge($this->plugins, $plugins);
        return $this;
    }

    /**
     * Called by TGMPA on tgmpa_register hook.
     * Don't call manually.
     *
     * @return $this
     * @throws Exception
     */
    public function hookTGMPA()
    {
        if(!$this->isInited()) {
            throw new Exception('Manager is not initialized.');
        }
        tgmpa($this->plugins, array(
            'dismissable' => false
        ));
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
     * Check if the a plugin with the specified slug is active.
     * TGMPA has a useful method for this, but TGMPA is only active in the admin context.
     *
     * @param $slug
     *
     * @return bool
     */
    public function isActive($slug)
    {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';

        foreach(get_plugins() as $key => $options) {
            if(0 === strpos($key, $slug . '/')) {
                return is_plugin_active($key);
            }
        }

        return false;
    }

    /**
     * Check if all plugins marked with required => true are active.
     *
     * @return bool
     */
    public function isActiveAll()
    {
        foreach($this->plugins as $plugin) {
            if(isset($plugin['required']) && $plugin['required'] && !$this->isActive($plugin['slug'])) {
                return false;
            }
        }
        return true;
    }
}