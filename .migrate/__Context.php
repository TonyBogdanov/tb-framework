<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * All components should rely on this to determine the runtime context.
 * Allows for easy mock.
 *
 * Class TB_Context
 */
class TB_Context extends TB_Trait_AppAware_Constructor
{
    /**
     * @var bool
     */
    protected $theme;

    /**
     * @var bool
     */
    protected $plugin;

    /**
     * @var string
     */
    protected $pluginBase;

    /**
     * @var string
     */
    protected $pluginFile;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $name;

    /**
     * @return bool
     */
    public function isTheme()
    {
        if(!isset($this->theme)) {
            $this->theme = realpath(get_stylesheet_directory()) === TB_APP_ROOT;
        }
        return $this->theme;
    }

    /**
     * @param bool $theme
     *
     * @return TB_Context
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPlugin()
    {
        if(!isset($this->plugin)) {
            $this->plugin = is_file($this->getPluginFile()) &&
                            $this->getPluginBase() === realpath(plugin_dir_path($this->getPluginFile()));
        }
        return $this->plugin;
    }

    /**
     * @param bool $plugin
     *
     * @return TB_Context
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;
        return $this;
    }

    /**
     * @return string
     */
    public function getPluginBase()
    {
        if(!isset($this->pluginBase)) {
            $this->pluginBase = realpath(TB_ROOT . '/..');
        }
        return $this->pluginBase;
    }

    /**
     * @return string
     */
    public function getPluginFile()
    {
        if(!isset($this->pluginFile)) {
            $this->pluginFile = $this->getPluginBase() . DIRECTORY_SEPARATOR . basename($this->getPluginBase()) . '.php';
        }
        return $this->pluginFile;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        if(!isset($this->slug)) {
            $this->slug = $this->isTheme() ? get_stylesheet() : basename($this->getPluginBase());
        }
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return TB_Context
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if(!isset($this->name)) {
            if($this->isTheme()) {
                $this->name = wp_get_theme()->get('Name');
            } else {
                $plugin = get_plugin_data($this->getPluginFile());
                $this->name = $plugin['Name'];
            }
        }
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return TB_Context
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}