<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Util_Filesystem
 */
class TB_Util_Filesystem
{
    const FILTER_FILES = 1;
    const FILTER_DIRS = 2;
    const FILTER_ANY = 3;

    /**
     * @return WP_Filesystem_Base
     */
    protected static function fs()
    {
        global $wp_filesystem;

        if(!isset($wp_filesystem)) {
            require_once trailingslashit(ABSPATH) . 'wp-admin/includes/file.php';
            WP_Filesystem();
        }

        return $wp_filesystem;
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public static function isFile($file)
    {
        return self::fs()->is_file($file);
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public static function isDir($path)
    {
        return self::fs()->is_dir($path);
    }

    /**
     * @param $path
     *
     * @return bool|string
     */
    public static function read($path)
    {
        return self::fs()->get_contents($path);
    }

    /**
     * Writes a file ensuring all parent directories exist.
     *
     * @param $path
     * @param $contents
     * @param bool $mode
     * @param bool $chmod
     * @param bool $chown
     * @param bool $chgrp
     *
     * @return bool
     */
    public static function write($path, $contents, $mode = false, $chmod = false, $chown = false, $chgrp = false)
    {
        if(!self::createDirectoryRecursive(dirname($path), $chmod, $chown, $chgrp)) {
            return false;
        }
        return self::fs()->put_contents($path, $contents, $mode);
    }

    /**
     * @param $path
     * @param bool $chmod
     * @param bool $chown
     * @param bool $chgrp
     *
     * @return bool
     */
    public static function createDirectoryRecursive($path, $chmod = false, $chown = false, $chgrp = false)
    {
        if(self::fs()->is_dir($path)) {
            return true;
        }

        $parent = dirname($path);
        if(!self::fs()->is_dir($parent)) {
            if(!self::createDirectoryRecursive($parent, $chmod, $chown, $chgrp)) {
                return false;
            }
        }

        if(!self::fs()->mkdir($path, $chmod, $chown, $chgrp)) {
            return false;
        }

        // protect the cache dir
        if(realpath($path) === realpath(TB_Util::getCachePath())) {
            self::write(TB_Util::getCachePath('.htaccess'), 'Options -Indexes');
        }

        return true;
    }

    /**
     * @param array $scan
     * @param string $path
     * @param int $filter
     *
     * @return array
     */
    public static function flatten(array $scan, $path = '', $filter = self::FILTER_ANY)
    {
        $flat = array();

        /** @var array $item */
        foreach($scan as $item) {
            if('f' == $item['type']) {
                if(self::FILTER_FILES === ($filter & self::FILTER_FILES)) {
                    $flat[] = $path . $item['name'];
                }
            } else if('d' == $item['type']) {
                if(self::FILTER_DIRS === ($filter & self::FILTER_DIRS)) {
                    $flat[] = $path . $item['name'];
                }
                $flat = array_merge(
                    $flat,
                    self::flatten($item['files'], $path . $item['name'] . DIRECTORY_SEPARATOR, $filter)
                );
            }
        }

        return $flat;
    }

    /**
     * @param $path
     * @param bool $include_hidden
     * @param bool $recursive
     * @param int $filter
     *
     * @return array|bool
     */
    public static function scan($path, $include_hidden = true, $recursive = false, $filter = self::FILTER_ANY)
    {
        $path = realpath($path);
        if(!$path) {
            return array();
        }

        $path .= DIRECTORY_SEPARATOR;
        return self::flatten(self::fs()->dirlist($path, $include_hidden, $recursive), $path, $filter);
    }

    /**
     * @param $path
     * @param bool $recursive
     *
     * @return array|bool
     */
    public static function scanDirs($path, $recursive = false)
    {
        return self::scan($path, true, $recursive, self::FILTER_DIRS);
    }

    /**
     * @param $path
     * @param bool $recursive
     *
     * @return array|bool
     */
    public static function scanFiles($path, $recursive = false)
    {
        return self::scan($path, true, $recursive, self::FILTER_FILES);
    }

    /**
     * @param $path
     * @param bool $recursive
     *
     * @return array
     */
    public static function scanAny($path, $recursive = false)
    {
        return self::scan($path, true, $recursive);
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public static function delete($path)
    {
        if(self::fs()->is_file($path)) {
            return self::fs()->delete($path);
        } else if(self::fs()->is_dir($path)) {
            return self::fs()->rmdir($path);
        }

        return false;
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public static function deleteRecursive($path)
    {
        if(self::fs()->is_file($path)) {
            return self::fs()->delete($path);
        } else if(self::fs()->is_dir($path)) {
            foreach(self::scanAny($path) as $item) {
                if(!self::deleteRecursive($item)) {
                    return false;
                }
            }
            return self::fs()->rmdir($path);
        }

        return false;
    }
}