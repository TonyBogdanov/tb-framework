<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Framework\Kernel;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use TB\Framework\Bundle;
use TB\Framework\Bundle\AbstractBundle;
use TB\Framework\Util\ExceptionHelper;

class Kernel
{
    /**
     * Environments.
     */
    const ENV_DEVELOPMENT = 'dev';
    const ENV_PRODUCTION = 'prod';

    /**
     * Holds a singleton reference to the global service manager / container.
     *
     * @var ContainerInterface
     */
    protected static $container;

    /**
     * Bootstrap the kernel and save singleton references to global resources like the service manager.
     *
     * @param string $environment
     * @throws KernelException
     */
    public static function bootstrap($environment = self::ENV_PRODUCTION)
    {
        // only allow single bootstrap call
        if (isset(self::$container)) {
            throw new KernelException(ExceptionHelper::format(':kernel has been bootstrapped already.', [
                'kernel' => __CLASS__
            ]));
        }

        // init container
        self::$container = new ContainerBuilder(new ParameterBag([
            'env' => $environment
        ]));

        // load framework bundle
        self::loadBundle(Bundle::class);
    }

    /**
     * Require and load (if not already loaded) a bundle and all of it's dependencies.
     *
     * @param $bundleClass
     * @throws KernelException
     */
    public static function loadBundle($bundleClass)
    {
        if (!isset(self::$container)) {
            throw new KernelException(ExceptionHelper::format(':kernel is not bootstrapped. You must call' .
                ' :bootstrap first.', [
                'kernel' => __CLASS__,
                'bootstrap' => __CLASS__ . '::bootstrap'
            ]));
        }

        if (!is_string($bundleClass) || !is_a($bundleClass, AbstractBundle::class, true)) {
            throw new KernelException(ExceptionHelper::format(':method expects a valid bundle class name' .
                ' (must extend :abstract). Got: :value.', [
                'method' => __METHOD__,
                'abstract' => AbstractBundle::class,
                'value' => $bundleClass
            ]));
        }

        /** @var AbstractBundle $bundle */
        $bundle = new $bundleClass(self::$container);

        // load bundle dependencies
        foreach ($bundle->getDependencies() as $dependency) {
            self::loadBundle($dependency);
        }

        // save a reference
        self::$container->set(get_class($bundle), $bundle);

        // load configuration
        if (0 < count($config = $bundle->getConfig())) {
            $loader = new YamlFileLoader(self::$container, new FileLocator());
            foreach ($config as $path) {
                $loader->load($path);
            }
        }

        // bootstrap bundle
        $bundle->bootstrap();
    }
}