<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\ServiceManager;

use TB\Initializable\InitializableInterface;

/**
 * Generic service (resource) manager.
 *
 * Class ServiceManager
 * @package TB
 */
class ServiceManager
{
    /**
     * Holds <resource name> => <class name> resolutions.
     *
     * @var array
     */
    protected $resolutions = [];

    /**
     * Resolves the specified resource name and returns the corresponding entry from $resolutions.
     * Will call bind() on resources which can be implicitly resolved.
     *
     * E.g. "this.is.the_class" will be automatically resolved to \This\Is\TheClass.
     *
     * @param string $name
     * @return array
     * @throws \Exception
     */
    protected function resolve($name)
    {
        if (!array_key_exists($name, $this->resolutions)) {
            // implicit resolution
            $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
            $className = '\\' . str_replace(' ', '\\', ucwords(str_replace('.', ' ', $className)));

            // TB is special
            if ('\\Tb' == substr($className, 0, 3)) {
                $className = '\\TB' . substr($className, 3);
            }

            if (class_exists($className)) {
                $this->bind($name, $className);
            } else {
                throw new \Exception('Resource with name "' . $name . '" is not registered with the service manager' .
                                    ' and cannot be implicitly resolved (tried ' . $className . '), call bind("' .
                                    $name . '", <ClassName>).');
            }
        }

        return $this->resolutions[$name];
    }

    /**
     * Returns TRUE if a resource with the specified name has been previously registered.
     * This will return FALSE if bind() has never been called for that name even if it can be implicitly resolved.
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->resolutions);
    }

    /**
     * Register a resolution for the specified resource name.
     * Optionally you can add additional arguments to be passed to the class constructor when creating an instance.
     *
     * The resolution must be a class name or an object, in which case the constructor arguments will be ignored.
     *
     * @param string $name
     * @param string|object $resolution
     * @return $this
     * @throws \Exception
     */
    public function bind($name, $resolution)
    {
        if (is_object($resolution)) {
            $this->resolutions[$name] = [
                'object' => $resolution
            ];

            return $this;
        }

        if (is_string($resolution)) {
            $arguments = func_get_args();
            array_shift($arguments);
            array_shift($arguments);

            $this->resolutions[$name] = [
                'class' => $resolution,
                'arguments' => $arguments
            ];

            return $this;
        }

        throw new \Exception('Resolution must be a class name or an object, got: ' . gettype($resolution) . '.');
    }

    /**
     * Retrieves an instance of a previously registered resource by name.
     * If this is the first request and the resource is registered by a class name, it will be instantiated,
     * and any subsequent calls will return that instance.
     *
     * @param string $name
     * @return object
     * @throws \Exception
     */
    public function get($name)
    {
        $resolution = $this->resolve($name);

        if(array_key_exists('object', $resolution)) {
            return $resolution['object'];
        }

        return $this->resolutions[$name]['object'] = $this->create($name);
    }

    /**
     * Creates and returns a new instance of the resource registered with the specified class name.
     * If the resource is registered as an object instead of a class name, an exception will be thrown.
     *
     * Optionally you can specify an array of arguments to be passed onto the class constructor, overriding any
     * arguments registered in the resolutions.
     *
     * @param string $name
     * @param array|null $arguments
     * @return object
     * @throws \Exception
     */
    public function create($name, array $arguments = null)
    {
        $resolution = $this->resolve($name);

        if(!array_key_exists('class', $resolution)) {
            throw new \Exception('Resource with name "' . $name . '" is registered with an object instance, it' .
                                ' can only be retrieved with get().');
        }

        $object = call_user_func_array(
            [new \ReflectionClass($resolution['class']), 'newInstance'],
            isset($arguments) ? $arguments : $resolution['arguments']
        );

        if ($object instanceof ServiceManagerAwareInterface) {
            $object->setServiceManager($this);
        }
        if($object instanceof InitializableInterface) {
            $object->initialize();
        }

        return $object;
    }
}