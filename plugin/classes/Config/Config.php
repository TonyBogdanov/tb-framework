<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Config;

use TB\ServiceManager\ServiceManagerAwareInterface;
use TB\ServiceManager\ServiceManagerAwareTrait;

/**
 * Generic configuration holder.
 *
 * Class Config
 * @package TB
 */
class Config implements ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

    /**
     * Configuration holder.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Throws an exception if the specified key(s) do not exist in the configuration.
     * Use this to require configuration entries to be previously set.
     *
     * @param string $key
     * @return $this
     * @throws \Exception
     */
    public function depend($key)
    {
        foreach(func_get_args() as $key) {
            if(!$this->has($key)) {
                throw new \Exception('Missing required configuration entry: "' . $key . '".');
            }
        }
        return $this;
    }

    /**
     * Check whether a configuration entry exists.
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->config);
    }

    /**
     * Get a configuration entry.
     *
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->config[$key] : $default;
    }

    /**
     * Set a configuration entry (or multiple by passing an array as the key).
     *
     * @param string|array $key
     * @param mixed|null $value
     * @return $this
     */
    public function set($key, $value = null)
    {
        if(is_array($key)) {
            foreach($key as $subKey => $subValue) {
                $this->config[$subKey] = $subValue;
            }
            return $this;
        }

        $this->config[$key] = $value;
        return $this;
    }
}