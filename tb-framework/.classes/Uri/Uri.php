<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Uri;

use TB\ServiceManager\ServiceManagerAwareInterface;
use TB\ServiceManager\ServiceManagerAwareTrait;

/**
 * WordPress navigation manager.
 *
 * Class Uri
 * @package TB\Uri
 */
class Uri implements ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

    /**
     * Local absolute path to the app using this service.
     * This should either be a path to a WordPress theme or a plugin.
     *
     * @var string
     */
    protected $appPath;

    /**
     * Absolute URL to the app using this service.
     * This should either be a URL to a WordPress theme or a plugin.
     *
     * @var string
     */
    protected $appUrl;

    /**
     * Clean up path, remove duplicate slashes and normalize them.
     * "/" us used as a path separator as it will work across OSs in both paths and URLs.
     *
     * @param string $path
     * @return string
     */
    public function getCleanPath($path)
    {
        return trim(preg_replace('#[/\\\]+#', '/', $path), '/');
    }

    /**
     * Retrieves a path relative to the app's root.
     *
     * @param string $path
     * @return string
     * @throws \Exception
     */
    public function getAppPath($path = '')
    {
        if (!isset($this->appPath)) {
            throw new \Exception('App path is not set, please call setAppPath() first.');
        }
        return trailingslashit($this->appPath) . $this->getCleanPath($path);
    }

    /**
     * Retrieves a URL relative to the app's root.
     *
     * @param string $path
     * @return string
     * @throws \Exception
     */
    public function getAppUrl($path = '')
    {
        if (!isset($this->appUrl)) {
            throw new \Exception('App URL is not set, please call setAppPath() first.');
        }
        return trailingslashit($this->appUrl) . $this->getCleanPath($path);
    }

    /**
     * Retrieves a path relative to the framework's root.
     *
     * @param string $path
     * @return string
     * @throws \Exception
     */
    public function getFrameworkPath($path = '')
    {
        return trailingslashit(WP_PLUGIN_DIR) . 'tb-framework/' . $this->getCleanPath($path);
    }

    /**
     * Retrieves a URL relative to the framework's root.
     *
     * @param string $path
     * @return string
     * @throws \Exception
     */
    public function getFrameworkUrl($path = '')
    {
        return trailingslashit(WP_PLUGIN_URL) . 'tb-framework/' . $this->getCleanPath($path);
    }

    /**
     * Retrieve the current page URL.
     * Will not work in CLI mode.
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        $ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');
        $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . ($ssl ? 's' : '');
        $port = $_SERVER['SERVER_PORT'];
        $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
        $host = isset($host) ? $host : $_SERVER['SERVER_NAME'] . $port;
        return $protocol . '://' . $host . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
    }

    /**
     * @param string $appPath
     * @return $this
     * @throws \Exception
     */
    public function setAppPath($appPath)
    {
        if (!($real = realpath($appPath)) || !is_dir($real)) {
            throw new \Exception('Path "' . $appPath . '" is not a valid directory path.');
        }
        $this->appPath = $real;
        return $this;
    }

    /**
     * @param string $appUrl
     * @return Uri
     */
    public function setAppUrl($appUrl)
    {
        $this->appUrl = $appUrl;
        return $this;
    }
}