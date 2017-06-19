<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Util
 */
class TB_Util
{
    /**
     * @return string
     */
    public static function getCurrentUrl()
    {
        $ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');
        $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . ($ssl ? 's' : '');
        $port = $_SERVER['SERVER_PORT'];
        $port = ((!$ssl && $port=='80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
        $host = isset($host) ? $host : $_SERVER['SERVER_NAME'] . $port;
        return $protocol . '://' . $host . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
    }

    /**
     * @param $path
     *
     * @return string
     */
    public static function getPathStripLevels($path)
    {
        return preg_replace('/^(\.{1,2}[\\\\\/])+/', '', ltrim($path, '\\/'));
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function getUploadPath($path = '')
    {
        $wpud = wp_upload_dir();
        return realpath($wpud['basedir']) . '/' . self::getPathStripLevels($path);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function getUploadUri($path = '')
    {
        $wpud = wp_upload_dir();
        return trailingslashit($wpud['baseurl']) . self::getPathStripLevels($path);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function getCachePath($path = '')
    {
        return self::getUploadPath('tb-cache/' . self::getPathStripLevels($path));
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function getCacheUri($path = '')
    {
        return self::getUploadUri('tb-cache/' . self::getPathStripLevels($path));
    }

    /**
     * @param $string
     *
     * @return string
     */
    public static function webSafeBase64Encode($string)
    {
        return str_replace(array('+', '/', '='), array('-', '_', '_'), base64_encode($string));
    }

    /**
     * @param $string
     *
     * @return bool|string
     */
    public static function webSafeBase64Decode($string)
    {
        return base64_decode(str_replace(array('-', '_', '_'), array('+', '/', '='), $string));
    }

    /**
     * @param $string
     * @param bool $fromFile
     *
     * @return string
     * @throws Exception
     */
    public static function crc($string, $fromFile = false)
    {
        $algos = array_intersect(
            array('adler32', 'crc32', 'crc32b', 'md2', 'md4', 'md5', 'sha1'),
            hash_algos()
        );

        if(empty($algos)) {
            throw new Exception('No supported hashing algorithm');
        }

        return self::webSafeBase64Encode($fromFile ? hash_file(current($algos), $string, true) :
            hash(current($algos), $string, true));
    }

    /**
     * @param $path
     *
     * @return string
     */
    public static function crcFile($path)
    {
        return self::crc($path, true);
    }
}