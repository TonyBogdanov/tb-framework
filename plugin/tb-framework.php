<?php
/*
Plugin Name:    TB Framework
Plugin URI:     https://github.com/TonyBogdanov/tb-framework
Description:    A versatile WordPress framework for theme and plugin development
Version:        1.0.0
Author:         Tony Bogdanov
Author URI:     https://tonybogdanov.com
Text Domain:    tb
Domain Path:    /languages
*/

// register an activation hook to ensure the current environment meets the minimum requirements
if (!function_exists('tb_activation_hook')) {
    function tb_activation_hook()
    {
        // ini flags
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ini-flags.php';

        // error messages
        $errors = array();

        // a minimum PHP version of 5.3 is required
        if (version_compare('5.3', phpversion(), '>')) {
            $errors[] = '<h3>PHP version</h3>' .
                '<p>This plugin requires PHP version <strong>5.3</strong> or higher, but your current version is' .
                ' <strong>' . phpversion() . '</strong>, which is outdated, no longer maintained and has multiple' .
                ' security issues which will never be fixed.</p><p>To ensure proper operation it is strongly' .
                ' recommended to use PHP version <strong>7.0</strong> or higher on all WordPress powered' .
                ' websites.</p><p>Please consider contacting your system administrator / host provider and ask them' .
                ' to switch your version of PHP to a more recent one to ensure better security, stability and' .
                ' compatibility with modern software.</p><p>Additionally you can try to switch the version yourself' .
                ' (if your hosting supports it) by adding the following line to the <code>.htaccess</code> file in' .
                ' the root directory where WordPress is installed:</p>' .
                '<pre>AddHandler application/x-httpd-php53 .php</pre>';
        }

        // memory limit of at least 128M is required
        if (true !== ($memoryLimit = tb_ensure_memory_limit('128M'))) {
            $errors[] = '<h3>Memory limit</h3>' .
                '<p>This plugin requires PHP to have access to at least <strong>128 megabytes</strong> of' .
                ' memory in order to function properly, but the current limit is set to <strong>' .
                $memoryLimit . '</strong>. Please contact your server administrator / hosting provider for' .
                ' help on how to raise the limit (the <code>ini_set</code> directive did not work).</p>';
        }

        // tests passed?
        if (!empty($errors)) {
            wp_die(
                '<h1>Oh no&hellip;</h1>' .
                '<p><div class="error"><em>It looks like your server does not meet the minimum requirements' .
                ' needed to run this plugin. Here is a list of all compatibility checks that have failed and' .
                ' suggestions on how to fix them:</em></div></p>' . implode('', $errors) .
                '<p><a href="javascript:history.back()">Go back</a></p>'
            );
        }
    }
}

register_activation_hook(__FILE__, 'tb_activation_hook');

// if the plugin is active, assume all compatibility checks have passed successfully
// from this point on PHP 5.3+ support is assumed
if (!function_exists('is_plugin_active')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
if (is_plugin_active(plugin_basename(__FILE__))) {
    // ensure ini flags
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ini-flags.php';
    tb_ensure_memory_limit('128M');

    // bootstrap the plugin
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'bootstrap.php';
}