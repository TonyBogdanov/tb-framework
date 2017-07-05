<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Framework\Bundle;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TB\Framework\Util\ExceptionHelper;

abstract class AbstractBundle implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Bundle base URL cache.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Bundle base filesystem path cache.
     *
     * @var string
     */
    protected $basePath;

    /**
     * Retrieve the base URL path to the bundle.
     *
     * @return string
     */
    abstract protected function getBaseUrl();

    /**
     * Retrieve the base filesystem path to the bundle.
     *
     * @return string
     */
    abstract protected function getBasePath();

    /**
     * AbstractBundle constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get a list of dependency bundle class names.
     *
     * @return array
     */
    public function getDependencies()
    {
        return [];
    }

    /**
     * Get a list of YAML configuration files (paths).
     *
     * @return array
     */
    public function getConfig()
    {
        return [];
    }

    /**
     * Get the specified path relative to the bundle URL.
     *
     * @param string $path
     * @return string
     */
    public function getUrl($path = '')
    {
        if (!isset($this->baseUrl)) {
            $this->baseUrl = trailingslashit($this->getBaseUrl());
        }
        if (
            'http:' === strtolower(substr($path, 0, 5)) ||
            'https:' === strtolower(substr($path, 0, 6)) ||
            '//' === strtolower(substr($path, 0, 2))
        ) {
            return $path;
        }
        return $this->baseUrl . $path;
    }

    /**
     * Get the specified path relative to the bundle filesystem path.
     *
     * @param string $path
     * @return string
     */
    public function getPath($path = '')
    {
        if (!isset($this->basePath)) {
            $basePath = $this->getBasePath();
            $realPath = realpath($basePath);

            if (false === $realPath) {
                throw new Exception(ExceptionHelper::format(':path does not resolve to a valid filesystem path.', [
                    'path' => $basePath
                ]));
            }

            $this->basePath = trailingslashit(str_replace('\\', '/', $realPath));
        }
        if ('/' === $path[0]) {
            return $path;
        }
        return $this->basePath . $path;
    }

    /**
     * Bootstrap the bundle in the context of the passed service manager.
     */
    public function bootstrap()
    {}
}