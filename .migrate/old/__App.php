<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Global runtime context.
 * All interactions must happen through this.
 *
 * Class TB_App
 */
class TB_App
{
    const CONFIG_SLUG                       = 10;
    const CONFIG_NAME                       = 20;
    const CONFIG_TEXT_DOMAIN                = 30;
    const CONFIG_HTML5_SUPPORT              = 40;
    const CONFIG_HTML_IMPORTS_SUPPORT       = 50;
    const CONFIG_TITLE_TAG_SUPPORT          = 60;
    const CONFIG_POST_THUMBNAIL_SUPPORT     = 70;
    const CONFIG_CONTENT_WIDTH              = 100;
    const CONFIG_FRONTEND_STYLES            = 110;
    const CONFIG_FRONTEND_INLINE_STYLES     = 120;
    const CONFIG_FRONTEND_SCRIPTS           = 130;
    const CONFIG_FRONTEND_INLINE_SCRIPTS    = 140;
    const CONFIG_FRONTEND_CUSTOM_ELEMENTS   = 150;
    const CONFIG_BACKEND_STYLES             = 160;
    const CONFIG_BACKEND_SCRIPTS            = 170;
    const CONFIG_BACKEND_CUSTOM_ELEMENTS    = 180;
    const CONFIG_EDITOR_STYLES              = 190;
    const CONFIG_LOGIN_STYLES               = 200;
    const CONFIG_LOGIN_SCRIPTS              = 210;
    const CONFIG_ALLOW_INACTIVE_PLUGINS     = 220;

    /**
     * @var TB_Context
     */
    protected $context;

    /**
     * @var TB_Plugin_Manager
     */
    protected $pluginManager;

    /**
     * @var TB_URI_Manager
     */
    protected $uriManager;

    /**
     * @var TB_PostType_Manager
     */
    protected $postTypeManager;

    /**
     * @var TB_View_Manager
     */
    protected $viewManager;

    /**
     * @var TB_Image_Manager
     */
    protected $imageManager;

    /**
     * @var TB_MetaBox_Manager
     */
    protected $metaBoxManager;

    /**
     * @var TB_ShortCode_Manager
     */
    protected $shortCodeManager;

    /**
     * @var TB_VisualComposer_Manager
     */
    protected $visualComposerManager;

    /**
     * @var TB_Customizer_Manager
     */
    protected $customizerManager;

    /**
     * TB_App constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        parent::__construct($config);

        add_action('after_setup_theme', array($this, 'hookAfterSetupTheme'));
        add_action('init', array($this, 'hookInit'));
        add_action('wp_enqueue_scripts', array($this, 'hookEnqueueFrontendScripts'));
        add_action('admin_enqueue_scripts', array($this, 'hookEnqueueBackendScripts'));
        add_action('login_enqueue_scripts', array($this, 'hookEnqueueLoginScripts'));
        add_action('template_redirect', array($this, 'hookTemplateRedirect'));
        add_action('wp_head', array($this, 'hookWPHead'));
        add_action('wp_footer', array($this, 'hookWPFooter'));
        add_action('in_admin_header', array($this, 'hookInAdminHeader'));
        add_action('in_admin_footer', array($this, 'hookInAdminFooter'));

        add_filter('style_loader_tag', array($this, 'filterStyleLoaderTag'), 10, 2);
        add_filter('body_class', array($this, 'filterBodyClass'));
        add_filter('upload_mimes', array($this, 'filterUploadMimes'));

        if($this->context()->isTheme()) {
            add_action('after_switch_theme', array($this, 'hookThemeActivated'));
            add_action('switch_theme', array($this, 'hookThemeDeactivated'));
        } else if($this->context()->isPlugin()) {
            register_activation_hook($this->context()->getPluginFile(), array($this, 'hookPluginActivated'));
            register_deactivation_hook($this->context()->getPluginFile(), array($this, 'hookPluginDeactivated'));
        }
    }

    /**
     * @return $this
     */
    public function hookAfterSetupTheme()
    {
        if($this->hasConfig(self::CONFIG_TEXT_DOMAIN)) {
            load_theme_textdomain($this->config(self::CONFIG_TEXT_DOMAIN));
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function hookInit()
    {
        if($this->hasConfig(self::CONFIG_EDITOR_STYLES)) {
            foreach((array) $this->config(self::CONFIG_EDITOR_STYLES) as $style) {
                add_editor_style($style);
            }
        }

        // visual composer has an init hook which forces support for featured images
        if($this->hasConfig(self::CONFIG_POST_THUMBNAIL_SUPPORT) && !$this->config(self::CONFIG_POST_THUMBNAIL_SUPPORT)) {
            remove_theme_support('post-thumbnails');
        }

        return $this;
    }

    /**
     * @param $stylesContext
     * @param $scriptsContext
     *
     * @return $this
     */
    public function hookEnqueueScripts($stylesContext, $scriptsContext)
    {
        if($this->hasConfig($stylesContext)) {
            /** @var array $config */
            $config = $this->config($stylesContext);
            foreach($config as $item) {
                call_user_func_array('wp_enqueue_style', $item);
            }
        }

        if($this->hasConfig($scriptsContext)) {
            /** @var array $config */
            $config = $this->config($scriptsContext);
            foreach($config as $item) {
                call_user_func_array('wp_enqueue_script', $item);
            }
        }

        return $this;
    }

    /**
     * @return TB_App
     */
    public function hookEnqueueFrontendScripts()
    {
        return $this->hookEnqueueScripts(self::CONFIG_FRONTEND_STYLES, self::CONFIG_FRONTEND_SCRIPTS);
    }

    /**
     * @return TB_App
     */
    public function hookEnqueueBackendScripts()
    {
        return $this->hookEnqueueScripts(self::CONFIG_BACKEND_STYLES, self::CONFIG_BACKEND_SCRIPTS);
    }

    /**
     * @return TB_App
     */
    public function hookEnqueueLoginScripts()
    {
        return $this->hookEnqueueScripts(self::CONFIG_LOGIN_STYLES, self::CONFIG_LOGIN_SCRIPTS);
    }

    /**
     * @return $this
     */
    public function hookTemplateRedirect()
    {
        if(
            $this->hasConfig(self::CONFIG_ALLOW_INACTIVE_PLUGINS) &&
            !$this->config(self::CONFIG_ALLOW_INACTIVE_PLUGINS) &&
            !$this->plugin()->isActiveAll()
        ) {
            $this->view()->render('error', 'inactive_plugins');
            do_action('shutdown');
            exit;
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function hookWPHead()
    {
        if($this->hasConfig(self::CONFIG_FRONTEND_INLINE_STYLES)) {
            foreach((array) $this->config(self::CONFIG_FRONTEND_INLINE_STYLES) as $uri) {
                $content = TB_Util_Filesystem::read($uri);
                if($content) {
                    echo '<style>' . $content . '</style>' . PHP_EOL;
                }
            }
        }

        if($this->hasConfig(self::CONFIG_FRONTEND_INLINE_SCRIPTS)) {
            foreach((array) $this->config(self::CONFIG_FRONTEND_INLINE_SCRIPTS) as $uri) {
                $content = TB_Util_Filesystem::read($uri);
                if($content) {
                    echo '<script>' . $content . '</script>' . PHP_EOL;
                }
            }
        }

        if($this->hasConfig(self::CONFIG_FRONTEND_CUSTOM_ELEMENTS)) {
            foreach((array) $this->config(self::CONFIG_FRONTEND_CUSTOM_ELEMENTS) as $uri) {
                echo '<link rel="import" href="' . esc_url($uri) . '" />' . PHP_EOL;
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function hookWPFooter()
    {
        if($this->config(self::CONFIG_DISABLE_EMBEDJS)) {
            wp_dequeue_script('wp-embed');
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function hookThemeDeactivated()
    {
        // clear cache
        TB_Util_Filesystem::deleteRecursive(TB_Util::getCachePath());

        return $this;
    }

    /**
     * @return $this
     */
    public function hookPluginActivated()
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function hookPluginDeactivated()
    {
        // clear cache
        TB_Util_Filesystem::deleteRecursive(TB_Util::getCachePath());

        return $this;
    }

    /**
     * @param $html
     * @param $handle
     *
     * @return mixed
     */
    public function filterStyleLoaderTag($html, $handle)
    {
        $html = preg_replace('/#async([\'"])/', '$1 async', $html);

        if(!$this->hasConfig(self::CONFIG_HTML_IMPORTS_SUPPORT) || !$this->config(self::CONFIG_HTML_IMPORTS_SUPPORT)) {
            return $html;
        }

        global $wp_styles;

        $obj = $wp_styles->query($handle);
        if(false === $obj || 'html' != strtolower(pathinfo($obj->src, PATHINFO_EXTENSION))) {
            return $html;
        }

        $html = preg_replace('/\h*rel=([\'"])(.+?)([\'"])/i', ' rel=$1import$3', $html);
        $html = preg_replace('/\h*?(?:type|media)=[\'"].+?[\'"]/i', '', $html);

        return $html;
    }

    /**
     * @param array $classes
     *
     * @return array
     */
    public function filterBodyClass(array $classes)
    {
        return $classes;
    }

    /**
     * @param array $mimes
     *
     * @return array
     */
    public function filterUploadMimes(array $mimes = array())
    {
        $mimes['svg'] = 'image/svg+xml';
        $mimes['svgz'] = 'image/svg+xml';
        return $mimes;
    }

    /**
     * @return TB_Context
     */
    public function context()
    {
        if(!isset($this->context)) {
            $this->context = new TB_Context($this);
        }
        return $this->context;
    }

    /**
     * @return TB_Plugin_Manager
     */
    public function plugin()
    {
        if(!isset($this->pluginManager)) {
            $this->pluginManager = new TB_Plugin_Manager($this);
        }
        return $this->pluginManager;
    }

    /**
     * @return TB_URI_Manager
     */
    public function uri()
    {
        if(!isset($this->uriManager)) {
            $this->uriManager = new TB_URI_Manager($this);
        }
        return $this->uriManager;
    }

    /**
     * @return TB_PostType_Manager
     */
    public function postType()
    {
        if(!isset($this->postTypeManager)) {
            $this->postTypeManager = new TB_PostType_Manager($this);
        }
        return $this->postTypeManager;
    }

    /**
     * @return TB_View_Manager
     */
    public function view()
    {
        if(!isset($this->viewManager)) {
            $this->viewManager = new TB_View_Manager($this);
        }
        return $this->viewManager;
    }

    /**
     * @return TB_Image_Manager
     */
    public function image()
    {
        if(!isset($this->imageManager)) {
            $this->imageManager = new TB_Image_Manager($this);
        }
        return $this->imageManager;
    }

    /**
     * @return TB_MetaBox_Manager
     */
    public function metaBox()
    {
        if(!isset($this->metaBoxManager)) {
            $this->metaBoxManager = new TB_MetaBox_Manager($this);
        }
        return $this->metaBoxManager;
    }

    /**
     * @return TB_ShortCode_Manager
     */
    public function shortCode()
    {
        if(!isset($this->shortCodeManager)) {
            $this->shortCodeManager = new TB_ShortCode_Manager($this);
        }
        return $this->shortCodeManager;
    }

    /**
     * @return TB_VisualComposer_Manager
     */
    public function visualComposer()
    {
        if(!isset($this->visualComposerManager)) {
            $this->visualComposerManager = new TB_VisualComposer_Manager($this);
        }
        return $this->visualComposerManager;
    }

    /**
     * @return TB_Customizer_Manager
     */
    public function customizer()
    {
        if(!isset($this->customizerManager)) {
            $this->customizerManager = new TB_Customizer_Manager($this);
        }
        return $this->customizerManager;
    }
}