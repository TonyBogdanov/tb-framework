<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

// prevent direct access
defined('ABSPATH') || die;

if (!class_exists('TB')) {
    /**
     * TB Framework loader class, use this to register themes and plugins which rely on the framework.
     *
     * Class TB
     */
    class TB
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
         * Determine whether sensitive debug information can be safely displayed.
         *
         * @return bool
         */
        protected static function canDebug()
        {
            return defined('WP_DEBUG') && WP_DEBUG && current_user_can('administrator');
        }

        /**
         * Register the loader with WordPress.
         */
        protected static function register()
        {
            if (self::$registered) {
                return;
            }

            self::$registered = true;

            add_action('init', 'TB::__actionInit');
            add_action('admin_notices', 'TB::__actionAdminNotices');
        }

        /**
         * Register a generic notice.
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
         * Register a success message notification.
         *
         * @param string $message - the success message as HTML
         * @param array $hideOn - an array of screen IDs to hide the notice on
         */
        protected static function success($message, array $hideOn = array())
        {
            self::notice('notice notice-success', $message, $hideOn);
        }

        /**
         * Register an error message notification.
         *
         * @param string $message - the error message as HTML
         * @param array $hideOn - an array of screen IDs to hide the notice on
         */
        protected static function error($message, array $hideOn = array())
        {
            self::notice('notice notice-error', $message, $hideOn);
        }

        /**
         * Register a warning message notification.
         *
         * @param string $message - the warning message as HTML
         * @param array $hideOn - an array of screen IDs to hide the notice on
         */
        protected static function warning($message, array $hideOn = array())
        {
            self::notice('notice notice-warning', $message, $hideOn);
        }

        /**
         * Register a nag message notification.
         *
         * @param string $message - the nag message as HTML
         * @param array $hideOn - an array of screen IDs to hide the notice on
         */
        protected static function nag($message, array $hideOn = array())
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
         * Adds notices to notify the admin that one or more theme or plugins have conflicting version requirements.
         */
        protected static function addVersionConflictNotices()
        {
            $dependencies = array();

            if (isset(self::$theme)) {
                $dependencies[] = 'Theme <strong>' . esc_html(self::$theme['name']) . '</strong> requires' .
                    ' <strong>TB Framework</strong> version in the range <strong>' . self::$theme['min'] .
                    '</strong> &ndash; <strong>' . self::$theme['max'] . '</strong> (<abbr title="Up to,' .
                    ' but not including ' . self::$theme['max'] . '">exclusive</abbr>).';
            }
            foreach (self::$plugins as $plugin) {
                $dependencies[] = 'Plugin <strong>' . esc_html($plugin['name']) . '</strong> requires' .
                    ' <strong>TB Framework</strong> version in the range <strong>' . $plugin['min'] .
                    '</strong> &ndash; <strong>' . $plugin['max'] . '</strong> (<abbr title="Up to,' .
                    ' but not including ' . $plugin['max'] . '">exclusive</abbr>).';
            }

            self::error(
                '<p>One or more currently installed theme / plugins rely on conflicting versions of' .
                ' <strong>TB Framework</strong>.<br />Either contact the theme / plugins' .
                ' authors and ask them to make their items compatible with the latest version of the' .
                ' framework, or disable the ones causing the conflict.</p>' .
                '<p><strong>TB Framework</strong> has been deactivated.</p>' .
                '<ul><li>' . implode('</li><li>', $dependencies) . '</li></ul>'
            );
        }

        /**
         * Adds notices to notify the admin that one or more theme or plugins have version requirements which are not
         * met by the currently installed version of the framework.
         *
         * @param bool $showDeactivated - whether to show the "framework has been deactivated" notice
         */
        protected static function addVersionNotSupportedNotices($showDeactivated = true)
        {
            $dependencies = array();

            if (isset(self::$theme)) {
                $dependencies[] = 'Theme <strong>' . esc_html(self::$theme['name']) . '</strong> requires' .
                    ' <strong>TB Framework</strong> version in the range <strong>' . self::$theme['min'] .
                    '</strong> &ndash; <strong>' . self::$theme['max'] . '</strong> (<abbr title="Up to,' .
                    ' but not including ' . self::$theme['max'] . '">exclusive</abbr>).';
            }
            foreach (self::$plugins as $plugin) {
                $dependencies[] = 'Plugin <strong>' . esc_html($plugin['name']) . '</strong> requires' .
                    ' <strong>TB Framework</strong> version in the range <strong>' . $plugin['min'] .
                    '</strong> &ndash; <strong>' . $plugin['max'] . '</strong> (<abbr title="Up to,' .
                    ' but not including ' . $plugin['max'] . '">exclusive</abbr>).';
            }

            $pluginData = get_file_data(trailingslashit(WP_PLUGIN_DIR) . 'tb-framework/tb-framework.php',
                array('version' => 'version'));

            self::error(
                '<p>One or more currently installed theme / plugins rely on a version of' .
                ' <strong>TB Framework</strong> which differs from the currently installed: <strong>' .
                esc_html($pluginData['version']) . '</strong>.<br />Either install the proper version of' .
                ' the framework, or disable the items causing the conflict.</p>' . ($showDeactivated ?
                    '<p><strong>TB Framework</strong> has been deactivated.</p>' : '') .
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

            self::nag(
                '<p>Please ' . ($installed ? 'activate' : 'install') . ' <strong>TB Framework</strong> required by' .
                $dependencies . '.</p>' .
                '<p><a href="' . esc_url(admin_url('plugins.php?page=' . ($installed ? 'activate' : 'install') .
                    '-tb-framework')) . '"><strong>Begin ' . ($installed ? 'activation' : 'installation') .
                '</strong></a></p>',
                array('plugins_page_' . ($installed ? 'activate' : 'install') . '-tb-framework')
            );
        }

        /**
         * Determines if the TB Framework plugin is installed.
         *
         * @return bool
         */
        public static function isInstalled()
        {
            return is_file(WP_PLUGIN_DIR . '/tb-framework/tb-framework.php');
        }

        /**
         * Determines if the TB Framework plugin is activated.
         * This can also be used to validate if the plugin is installed, as it will fail if it isn't.
         *
         * @return bool
         */
        public static function isActivated()
        {
            if (!function_exists('is_plugin_active')) {
                require_once ABSPATH . '/wp-admin/includes/plugin.php';
            }
            return is_plugin_active('tb-framework/tb-framework.php');
        }

        /**
         * Determines if the currently installed version of TB Framework is within the specified version range.
         * This can also be used to validate if the plugin is activated, as it will fail if it isn't.
         *
         * @param array $versionRange - an array in the format: ['min' => ?, 'max' => ?]
         * @return bool
         */
        public static function isSupported(array $versionRange)
        {
            return
                defined('TB_FRAMEWORK') &&
                version_compare(TB_FRAMEWORK, $versionRange['min'], '>=') &&
                version_compare(TB_FRAMEWORK, $versionRange['max'], '<');
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
                'main' => realpath($functionsPath),
                'dir' => realpath(dirname($functionsPath)),
                'bootstrap' => realpath($bootstrapPath),
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
                'main' => realpath($pluginFilePath),
                'dir' => realpath(dirname($pluginFilePath)),
                'bootstrap' => realpath($bootstrapPath),
                'min' => $minVersion,
                'max' => $maxVersion
            );
            self::register();
        }

        /**
         * Include a gated version of the file specified in $path if the TB Framework is properly installed, activated
         * and supported by the previously registered theme or plugin, to which this file belongs.
         *
         * Using this methods ensures you can use and rely on all server environment features listed as dependencies
         * for running the framework, e.g. PHP 5.3.
         *
         * If not specified, the gated path will be automatically determined as the path of the source file relative to
         * the theme or plugin root, prepended by a "gated" folder. Example: /path/to/theme/my/file.php will become:
         * /path/to/theme/gated/my/file.php.
         *
         * @param string $path - path to the file to be gated
         * @param string|null $gatedPath - path to the gated file, can be determined automatically
         * @param bool $silent - will fail silently (hide wp errors) if set to TRUE
         * @return mixed
         */
        public static function gate($path, $gatedPath = null, $silent = false)
        {
            // determine the registered app
            $app = false;

            /** @var array $item */
            foreach (array_merge(array(self::$theme), self::$plugins) as $item) {
                if (0 === strpos($path, $item['dir'])) {
                    $app = $item;
                    break;
                }
            }

            // requested app isn't registered?
            // this should never really happen
            if (!$app) {
                if ($silent) {
                    return null;
                }
                wp_die(
                    '<h1>Oops&hellip;</h1>' .
                    (self::canDebug() ?
                        '<p>The requested path <strong>' . esc_html($path) . '</strong> cannot be gated because' .
                        ' it does not belong to a registered theme or plugin.</p>' :
                        '<p>An internal error occurred, please try again later.</p>')
                );
            }

            // determine the gated path automatically
            if (!isset($gatedPath)) {
                $gatedPath = trailingslashit($app['dir']) . 'gated' . substr($path, strlen($app['dir']));
            }

            // validate the gated path
            if (!is_file($gatedPath)) {
                if ($silent) {
                    return null;
                }
                wp_die(
                    '<h1>Oops&hellip;</h1>' .
                    (self::canDebug() ?
                        '<p>Gated file <strong>' . esc_html($gatedPath) . '</strong> does not exist.</p>' :
                        '<p>An internal error occurred, please try again later.</p>')
                );
            }

            // validate framework
            // this will also validate if the framework is installed and activated
            if (!self::isSupported($app)) {
                if ($silent) {
                    return null;
                }
                wp_die(
                    '<h1>Oops&hellip;</h1>' .
                    (self::canDebug() ?
                        '<p>This feature requires the <strong>TB Framework</strong> plugin to be properly installed' .
                        ' and activated.<br />Please visit the administrative panel for more information.</p>' :
                        '<p>An internal error occurred, please try again later.</p>')
                );
            }

            // all good
            return require $gatedPath;
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
                add_action('admin_menu', 'TB::__actionAdminMenu' . ($installed ? 'Activate' : 'Install'));

                // bail early
                return;
            }

            // check for conflicts with the currently installed version of the framework
            if (!self::isSupported($versionRange)) {
                // deactivate the framework
                deactivate_plugins('tb-framework/tb-framework.php');

                // add notices
                self::addVersionNotSupportedNotices();

                // bail early
                return;
            }
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
                'TB::__installPage'
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
                'TB::__activatePage'
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

        /**
         * Activate the framework plugin.
         */
        public static function __activatePage()
        {
            echo '<div class="wrap">';
            echo '<h1>Activating TB Framework</h1>';

            // determine a valid version range requested by registered components
            // no need to check for version collisions throughout dependents
            // if there are any, the plugin will be deactivated before we can land on this page
            $versionRange = self::resolveVersionRange();

            // mock the current framework version
            $pluginData = get_file_data(trailingslashit(WP_PLUGIN_DIR) . 'tb-framework/tb-framework.php',
                array('version' => 'version'));
            define('TB_FRAMEWORK', $pluginData['version']);

            // check for conflicts with the currently installed version of the framework
            if (!self::isSupported($versionRange)) {
                // add notices
                self::addVersionNotSupportedNotices(false);
            } else {
                // activate the plugin
                activate_plugin('tb-framework/tb-framework.php');

                // success?
                if (self::isActivated()) {
                    self::success('<p>Successfully activated <strong>TB Framework</strong>.</p>');
                } else {
                    self::error('<p>Could not activate <strong>TB Framework</strong>.</p>');
                }
            }

            // display any notices
            self::__actionAdminNotices();

            echo '<p><a href="' . esc_url(admin_url('plugins.php')) .
                '" target="_parent">Return to Plugins</a></p>';

            echo '</div>';
        }
    }
}