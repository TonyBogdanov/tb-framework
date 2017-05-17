<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

// prevent direct access
defined('ABSPATH') || die;

if (!class_exists('TBLoader')) {
    /**
     * TB Framework loader class, use this to register themes and plugins which rely on the framework.
     *
     * Class TBLoader
     */
    class TBLoader
    {
        /**
         * Whether the loader has registered with WordPress.
         * Prevents multiple registrations from multiple dependents.
         *
         * @var bool
         */
        protected static $registered = false;

        /**
         * Holds notifications to be displayed in the admin panel.
         *
         * @var array
         */
        protected static $notifications = array();

        /**
         * Holds a reference to a registered theme.
         *
         * @var array
         */
        protected static $theme;

        /**
         * Holds references to registered plugins.
         *
         * @var array
         */
        protected static $plugins = array();

        /**
         * Register the loader with WordPress.
         */
        protected static function register()
        {
            if (self::$registered) {
                return;
            }

            self::$registered = true;

            add_action('init', 'TBLoader::__actionInit');
            add_action('admin_notices', 'TBLoader::__actionAdminNotices');
        }

        /**
         * Register a generic notice
         *
         * @param string $type - type of notice (HTML class)
         * @param string $message - the message as HTML
         * @param array $hideOn - an array of screen IDs to hide the notice on
         */
        protected static function notice($type, $message, array $hideOn = array())
        {
            self::$notifications[] = array(
                'type' => $type,
                'hide_on' => $hideOn,
                'message' => $message
            );
        }

        /**
         * Register an error message notification
         *
         * @param string $message - the error message as HTML
         * @param array $hideOn - an array of screen IDs to hide the notice on
         */
        protected static function error($message, array $hideOn = array())
        {
            self::notice('error', $message, $hideOn);
        }

        /**
         * Register a warning message notification
         *
         * @param string $message - the warning message as HTML
         * @param array $hideOn - an array of screen IDs to hide the notice on
         */
        protected static function warning($message, array $hideOn = array())
        {
            self::notice('update-nag', $message, $hideOn);
        }

        /**
         * Attempts to resolve a version range requirement from the registered theme and plugins.
         * Returns an array in the format array('min' => string, 'max' => string) or FALSE on failure.
         *
         * @return array|bool
         */
        protected static function resolveVersionRange()
        {
            $resolved = false;
            $min = '0';
            $max = (string) PHP_INT_MAX;

            if (isset(self::$theme)) {
                if (version_compare(self::$theme['min'], $min, '>')) {
                    $resolved = true;
                    $min = self::$theme['min'];
                }
                if (version_compare(self::$theme['max'], $max, '<')) {
                    $resolved = true;
                    $max = self::$theme['max'];
                }
            }

            foreach (self::$plugins as $plugin) {
                if (version_compare($plugin['min'], $min, '>')) {
                    $resolved = true;
                    $min = $plugin['min'];
                }
                if (version_compare($plugin['max'], $max, '<')) {
                    $resolved = true;
                    $max = $plugin['max'];
                }
            }

            if (!$resolved) {
                return false;
            }

            return version_compare($min, $max, '<=') ? array(
                'min' => $min,
                'max' => $max
            ) : false;
        }

        /**
         * Determines if the TB Framework plugin is installed.
         *
         * @return bool
         */
        protected static function isInstalled()
        {
            return is_file(WP_PLUGIN_DIR . '/tb-framework/tb-framework.php');
        }

        /**
         * Determines if the TB Framework plugin is activated.
         *
         * @return bool
         */
        protected static function isActivated()
        {
            if (!function_exists('is_plugin_active')) {
                require_once ABSPATH . '/wp-admin/includes/plugin.php';
            }
            return is_plugin_active('tb-framework/tb-framework.php');
        }

        /**
         * Adds notices to notify the admin that one or more theme or plugins have conflicting version requirements.
         */
        protected static function addVersionConflictNotices()
        {
            $dependencies = array();

            if (isset(self::$theme)) {
                $dependencies[] = 'Theme <strong>' . esc_html(self::$theme['name']) . '</strong> requires' .
                    ' <strong>TB Framework</strong> version in the range <strong>' . self::$theme['min'] .
                    '</strong> &ndash; <strong>' . self::$theme['max'] . '</strong> (<abbr title="Up to,' .
                    ' but not including ' . self::$theme['max'] . '">excluded</abbr>).';
            }
            foreach (self::$plugins as $plugin) {
                $dependencies[] = 'Plugin <strong>' . esc_html($plugin['name']) . '</strong> requires' .
                    ' <strong>TB Framework</strong> version in the range <strong>' . $plugin['min'] .
                    '</strong> &ndash; <strong>' . $plugin['max'] . '</strong> (<abbr title="Up to,' .
                    ' but not including ' . $plugin['max'] . '">excluded</abbr>).';
            }

            self::error(
                '<p>The currently installed theme / plugins rely on conflicting versions of the' .
                ' TB Framework thus cannot work together.<br />Either contact the theme / plugins' .
                ' authors and ask them to make their product compatible with the latest version of the' .
                ' framework, or disable the ones causing the conflict:</p>' .
                '<ul><li>' . implode('</li><li>', $dependencies) . '</li></ul>'
            );
        }

        /**
         * Adds notices to notify the admin that the plugin is not currently installed / activated.
         *
         * @param $installed - set to TRUE if the plugin is installed, but not activated, FALSE if not installed
         */
        protected static function addPluginInactiveNotices($installed)
        {
            $dependencies = '';

            if (isset(self::$theme)) {
                $dependencies .= ' the <strong>' . esc_html(self::$theme['name']) . '</strong> theme';
                if (!empty(self::$plugins)) {
                    $dependencies .= ', and';
                }
            }
            if (!empty(self::$plugins)) {
                $dependencies .= ' the';
                foreach (self::$plugins as $index => $plugin) {
                    $dependencies .= 0 == $index ? ' ' : ($index + 1 < count(self::$plugins) ? ', ' : ' and ');
                    $dependencies .= '<strong>' . esc_html($plugin['name']) . '</strong>';
                }
                $dependencies .= ' plugin' . (1 < count(self::$plugins) ? 's' : '');
            }

            self::warning(
                '<p>Please <a href="' . esc_url(admin_url('plugins.php?page=' . ($installed ? 'activate' : 'install') .
                    '-tb-framework')) . '">' . ($installed ? 'activate' : 'install') .
                ' the TB Framework</a> required by' . $dependencies . '.</p>',
                array('plugins_page_' . ($installed ? 'activate' : 'install') . '-tb-framework')
            );
        }

        /**
         * Register a theme with the framework.
         *
         * @param $name - human readable name of the theme
         * @param $functionsPath - full path to the functions.php file of the theme
         * @param $bootstrapPath - full path to a bootstrap file to be executed when theme dependencies have resolved
         * @param $minVersion - minimum supported framework version (inclusive)
         * @param $maxVersion - maximum supported framework version (exclusive)
         */
        public static function registerTheme(
            $name,
            $functionsPath,
            $bootstrapPath,
            $minVersion,
            $maxVersion
        ) {
            self::$theme = array(
                'name' => $name,
                'main' => $functionsPath,
                'bootstrap' => $bootstrapPath,
                'min' => $minVersion,
                'max' => $maxVersion
            );
            self::register();
        }

        /**
         * Register a plugin with the framework.
         *
         * @param $name - human readable name of the plugin
         * @param $pluginFilePath - full path to the main plugin file
         * @param $bootstrapPath - full path to a bootstrap file to be executed when plugin dependencies have resolved
         * @param $minVersion - minimum supported framework version (inclusive)
         * @param $maxVersion - maximum supported framework version (exclusive)
         */
        public static function registerPlugin(
            $name,
            $pluginFilePath,
            $bootstrapPath,
            $minVersion,
            $maxVersion
        ) {
            self::$plugins[] = array(
                'name' => $name,
                'main' => $pluginFilePath,
                'bootstrap' => $bootstrapPath,
                'min' => $minVersion,
                'max' => $maxVersion
            );
            self::register();
        }

        /**
         * @hook init
         */
        public static function __actionInit()
        {
            // determine a valid version range requested by registered components
            $versionRange = self::resolveVersionRange();

            // detect version collisions throughout dependents
            if (!$versionRange) {
                // add notices
                self::addVersionConflictNotices();

                // bail early
                return;
            }

            // prompt for plugin installation / activation
            if (!self::isActivated()) {
                // add notices
                self::addPluginInactiveNotices($installed = self::isInstalled());

                // register install / activate action page
                add_action('admin_menu', 'TBLoader::__actionAdminMenu' . ($installed ? 'Activate' : 'Install'));

                // bail early
                return;
            }

            // resolve conflicts with the currently installed version of the framework
            // todo
            exit(__METHOD__);
        }

        /**
         * @hook admin_notices
         */
        public static function __actionAdminNotices()
        {
            // get current screen
            if (!function_exists('get_current_screen')) {
                require_once ABSPATH . '/wp-admin/includes/screen.php';
            }
            $screen = get_current_screen();

            // display eligible notices
            foreach (self::$notifications as $notification) {
                if (isset($screen) && in_array($screen->id, $notification['hide_on'])) {
                    continue;
                }
                echo '<div class="' . esc_attr($notification['type']) . '">' . $notification['message'] . '</div>';
            }
        }

        /**
         * @hook admin_menu
         */
        public static function __actionAdminMenuInstall()
        {
            add_plugins_page(
                'Install TB Framework',
                'Install TB Framework',
                'install_plugins',
                'install-tb-framework',
                'TBLoader::__installPage'
            );
        }

        /**
         * @hook admin_menu
         */
        public static function __actionAdminMenuActivate()
        {
            add_plugins_page(
                'Activate TB Framework',
                'Activate TB Framework',
                'activate_plugins',
                'activate-tb-framework',
                'TBLoader::__activatePage'
            );
        }

        /**
         * Boot up the framework installation process.
         */
        public static function __installPage()
        {
            if (!class_exists('WP_Upgrader')) {
                require_once ABSPATH . '/wp-admin/includes/class-wp-upgrader.php';
            }
            $installer = new Plugin_Upgrader(new Plugin_Installer_Skin(array(
                'title' => 'Installing TB Framework'
            )));
            $installer->install('https://repo.tonybogdanov.com/download/version-range/tb-framework/' .
                implode('_', self::resolveVersionRange()) . '.zip');
        }

        public static function __activatePage()
        {
            echo __METHOD__;
        }
    }
}