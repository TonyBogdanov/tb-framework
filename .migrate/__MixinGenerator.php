<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_MixinGenerator
 */
class TB_MixinGenerator
{
    const PREFIX = 'Mixin__';

    /**
     * @var string
     */
    protected static $path;

    /**
     * @param $path
     *
     * @return bool
     */
    protected static function isDirty($path)
    {
        if(!TB_Util_Filesystem::isFile($path)) {
            return true;
        }

        // skip dirty checks if WP is in production mode
        if((!defined('TB_DEVELOPMENT') || !TB_DEVELOPMENT) && (!defined('WP_DEBUG') || !WP_DEBUG)) {
            return false;
        }

        if(
            !preg_match('/^<\?php' . PHP_EOL . '\/\/ (?P<head>.+?)' . PHP_EOL . '/i', TB_Util_Filesystem::read($path),
                    $matches) ||
            !is_array($head = json_decode($matches['head'], true)) ||
            md5_file($head[0]) != $head[1]
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param $path
     *
     * @throws Exception
     */
    public static function register($path)
    {
        if(isset(self::$path)) {
            throw new Exception('The mixin generator has already registered');
        }

        if(!is_dir($path)) {
            if(!TB_Util_Filesystem::createDirectoryRecursive($path)) {
                throw new Exception('Invalid mixin path: ' . $path);
            }
        }

        self::$path = realpath($path);
        spl_autoload_register('TB_MixinGenerator::load');
    }

    /**
     * @param $className
     *
     * @throws Exception
     */
    public static function load($className)
    {
        // only respond to mixins (classes starting with the proper prefix)
        if(self::PREFIX != substr($className, 0, ($mixinPrefixLength = strlen(self::PREFIX)))) {
            return;
        }

        $cache = self::$path . DIRECTORY_SEPARATOR . md5($className) . '.php';

        if(!self::isDirty($cache)) {
            require_once $cache;
            return;
        }

        $classes = explode('__', substr($className, $mixinPrefixLength));
        $firstClass = array_shift($classes);

        if(0 == count($classes)) {
            throw new Exception('Could not generate mixin ' . $className . ' because it consists of only one class,' .
                                ' extend directly from ' . $firstClass . ' instead');
        }

        // inherited parent class and interfaces
        $reflection = new ReflectionClass($firstClass);
        $fileName = $reflection->getFileName();
        $startLine = $reflection->getStartLine();
        $endLine = $reflection->getEndLine();
        $interfaces = $reflection->getInterfaceNames();

        if($reflection->getParentClass()) {
            array_unshift($classes, $reflection->getParentClass()->getName());
        }

        $head = array($fileName, md5_file($fileName));
        $body = implode(PHP_EOL, array_slice(explode(PHP_EOL, TB_Util_Filesystem::read($fileName)), $startLine,
            $endLine - $startLine));

        TB_Util_Filesystem::write($cache,
            '<' . '?php' . PHP_EOL .
            '// ' . json_encode($head) . PHP_EOL . PHP_EOL .
            'if(!class_exists(\'WP\')) {' . PHP_EOL .
            '    header(\'HTTP/1.0 403 Forbidden\');' . PHP_EOL .
            '    exit;' . PHP_EOL .
            '}' . PHP_EOL . PHP_EOL .
            '/**' . PHP_EOL .
            ' * Class ' . $firstClass . PHP_EOL .
            ' */' . PHP_EOL .
            ($reflection->isAbstract() ? 'abstract ' : '') . 'class ' . $className . PHP_EOL .
            '    extends ' . (1 < count($classes) ? self::PREFIX . implode('__', $classes) : $classes[0]) .
            (empty($interfaces) ? '' : PHP_EOL . '    implements ' . implode(', ', $interfaces)) .
            PHP_EOL . $body
        );

        require_once $cache;
    }
}