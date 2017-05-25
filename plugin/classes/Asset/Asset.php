<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Asset;

use TB\ServiceManager\ServiceManagerAwareInterface;
use TB\ServiceManager\ServiceManagerAwareTrait;

/**
 * Asset manager.
 *
 * Class Asset
 * @package TB\Asset
 */
class Asset implements ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

    /**
     * Enqueued scripts holder.
     *
     * @var array
     */
    protected $scripts = [];

    /**
     * Enqueued styles holder.
     *
     * @var array
     */
    protected $styles = [];

    /**
     * Enqueued (HTML) imports holder.
     *
     * @var array
     */
    protected $imports = [];

    /**
     * Allows for enqueueing special type of assets.
     *
     * @var array
     */
    protected $special = [
        'media' => false
    ];

    /**
     * @hook
     */
    public function __actionEnqueueScripts()
    {
        /** @var array $options */
        foreach($this->getScripts() as $name => $options) {
            wp_enqueue_script($name, $options['url'], $options['dependencies'], $options['version'], $options['in_footer']);
        }

        /** @var array $options */
        foreach($this->getStyles() as $name => $options) {
            wp_enqueue_style($name, $options['url'], $options['dependencies'], $options['version'], $options['media']);
        }
    }

    /**
     * @hook
     */
    public function __actionEnqueueImports()
    {
        foreach($this->getImports() as $url) {
            echo '<link rel="import" href="' . esc_url($url) . '" />' . PHP_EOL;
        }
    }

    /**
     * Enqueue a script.
     *
     * @param string $name
     * @param string $url
     * @param array $dependencies
     * @param string $version
     * @param bool $inFooter
     * @return $this
     */
    public function enqueueScript($name, $url = '', array $dependencies = [], $version = null, $inFooter = false)
    {
        $this->scripts[$name] = [
            'url' => $url,
            'dependencies' => $dependencies,
            'version' => $version,
            'in_footer' => $inFooter
        ];
        return $this;
    }

    /**
     * Enqueue a stylesheet.
     *
     * @param string $name
     * @param string $url
     * @param array $dependencies
     * @param string $version
     * @param string $media
     * @return $this
     */
    public function enqueueStyle($name, $url = '', array $dependencies = [], $version = null, $media = 'all')
    {
        $this->styles[$name] = [
            'url' => $url,
            'dependencies' => $dependencies,
            'version' => $version,
            'media' => $media
        ];
        return $this;
    }

    /**
     * Enqueue an import.
     *
     * @param string $url
     * @return $this
     */
    public function enqueueImport($url)
    {
        $this->imports[] = $url;
        return $this;
    }

    /**
     * Enqueue WP media.
     *
     * @return $this
     */
    public function enqueueMedia()
    {
        $this->special['media'] = true;
        return $this;
    }

    /**
     * @return array
     */
    public function getScripts()
    {
        return $this->scripts;
    }

    /**
     * @param array $scripts
     * @return Asset
     */
    public function setScripts(array $scripts)
    {
        $this->scripts = $scripts;
        return $this;
    }

    /**
     * @return array
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * @param array $styles
     * @return Asset
     */
    public function setStyles(array $styles)
    {
        $this->styles = $styles;
        return $this;
    }

    /**
     * @return array
     */
    public function getImports()
    {
        return $this->imports;
    }

    /**
     * @param array $imports
     * @return Asset
     */
    public function setImports(array $imports)
    {
        $this->imports = $imports;
        return $this;
    }

    /**
     * @return array
     */
    public function getSpecial()
    {
        return $this->special;
    }

    /**
     * @param array $special
     * @return Asset
     */
    public function setSpecial(array $special)
    {
        $this->special = $special;
        return $this;
    }
}