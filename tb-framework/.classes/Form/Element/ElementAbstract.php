<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Form\Element;

use TB\DOM\DOM;
use TB\Form\Decorator\DecoratorAbstract;
use TB\Form\Form;
use TB\ServiceManager\ServiceManagerAwareInterface;
use TB\ServiceManager\ServiceManagerAwareTrait;

/**
 * Base form element class.
 *
 * Class ElementAbstract
 * @package TB\Form
 */
abstract class ElementAbstract implements
    ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

    /**
     * A regex range of allowed characters for form element names.
     */
    const NAME_REGEX_RANGE = 'a-zA-Z0-9_-';

    /**
     * Filter modes.
     */
    const FILTER_MODE_SERIALIZE = 1;
    const FILTER_MODE_DESERIALIZE = 2;

    /**
     * The name of the form element.
     * Use getNameForRendering() to determine the actual HTML name in the context of parent elements.
     *
     * @var string
     */
    protected $name;

    /**
     * The value of the element.
     * This value can be of any type and must be compliant with the internal logic of the element.
     *
     * The related getters and setters should be used internally only.
     * To interact with the element see getData / setData / getSerializedData / setSerializedData.
     *
     * @var mixed
     */
    protected $value;

    /**
     * A flag to determine if the value has been set via a call to setValue.
     * This is used to determine when getValue should return the default element value (if available).
     *
     * @var bool
     */
    protected $valueIsset;

    /**
     * Custom element options.
     * Structure is determined by the element.
     *
     * @var array
     */
    protected $options;

    /**
     * A list of decorators to be applied when calling render.
     *
     * Decorators may change the appearance of the element, but they *must not* remove or change important attributes
     * of the base <input> element carrying the value. Such attributes are name and value.
     *
     * In theory no matter how complex an element's internal logic is, it must supply at least one <input> element
     * (can be hidden) holding the actual value that is to be submitted. In case more than one <input> element is found,
     * only the first one should be considered.
     *
     * Following this rule allows for easy and consistent decoration and element extending.
     *
     * @var array
     */
    protected $decorators = [];

    /**
     * TRUE => Filters to be applied when serializing the normalized value to a serialized, scalar format.
     * FALSE => Filters to be applied when deserializing the serialized, scalar value to a normalized (internal) format.
     *
     * @var array
     */
    protected $filters = [
        self::FILTER_MODE_SERIALIZE => null,
        self::FILTER_MODE_DESERIALIZE => null
    ];

    /**
     * Initialize default filters.
     *
     * @return $this
     */
    private function lazyInitFilters()
    {
        if (!isset($this->filters[self::FILTER_MODE_SERIALIZE])) {
            $this->filters[self::FILTER_MODE_SERIALIZE] = [];
            $this->addSerializationFilters($this->getDefaultSerializationFilters());
        }
        if (!isset($this->filters[self::FILTER_MODE_DESERIALIZE])) {
            $this->filters[self::FILTER_MODE_DESERIALIZE] = [];
            $this->addDeserializationFilters($this->getDefaultDeserializationFilters());
        }
        return $this;
    }

//    /**
//     * Register a filter.
//     *
//     * @param TB_Form_Filter $filter
//     * @param int $mode
//     *
//     * @return $this
//     */
//    private function addFilter(TB_Form_Filter $filter, $mode)
//    {
//        $this->lazyInitFilters();
//
//        $priority = $filter->getPriority();
//        if(!isset($this->filters[$mode][$priority])) {
//            $this->filters[$mode][$priority] = array();
//        }
//
//        $this->filters[$mode][$priority][] = $filter;
//        return $this;
//    }
//
//    /**
//     * Apply filters to the supplied value.
//     *
//     * @param mixed $value
//     * @param int $mode
//     *
//     * @return mixed
//     */
//    private function applyFilters($value, $mode)
//    {
//        $this->lazyInitFilters();
//        ksort($this->filters[$mode]);
//
//        /** @var array $filters */
//        foreach($this->filters[$mode] as $filters) {
//            /** @var TB_Form_Filter $filter */
//            foreach($filters as $filter) {
//                $value = $filter->filter($value, $this);
//            }
//        }
//
//        return $value;
//    }

    /**
     * Override this to supply default element serialization filters.
     *
     * @return array
     */
    protected function getDefaultSerializationFilters()
    {
        return [];
    }

    /**
     * Override this to supply default element serialization filters.
     *
     * @return array
     */
    protected function getDefaultDeserializationFilters()
    {
        return [];
    }

//    /**
//     * Add a serialization filter.
//     * Lower priority will result in earlier execution.
//     *
//     * Filters with the same priority are executed in the order they are registered.
//     *
//     * @param TB_Form_Filter $filter
//     * @return $this
//     */
//    protected function addSerializationFilter(TB_Form_Filter $filter)
//    {
//        return $this->addFilter($filter, self::FILTER_MODE_SERIALIZE);
//    }
//
//    /**
//     * Add multiple serialization filters.
//     *
//     * @param array $filters
//     * @return $this
//     */
//    protected function addSerializationFilters(array $filters)
//    {
//        /** @var TB_Form_Filter $filter */
//        foreach($filters as $filter) {
//            if($filter instanceof TB_Form_Filter) {
//                $this->addSerializationFilter($filter);
//            }
//        }
//        return $this;
//    }

//    /**
//     * Add a deserialization filter.
//     * Lower priority will result in earlier execution.
//     *
//     * Filters with the same priority are executed in the order they are registered.
//     *
//     * @param TB_Form_Filter $filter
//     * @return $this
//     */
//    protected function addDeserializationFilter(TB_Form_Filter $filter)
//    {
//        return $this->addFilter($filter, self::FILTER_MODE_DESERIALIZE);
//    }
//
//    /**
//     * Add multiple deserialization filters.
//     *
//     * @param array $filters
//     * @return $this
//     */
//    protected function addDeserializationFilters(array $filters)
//    {
//        /** @var TB_Form_Filter $filter */
//        foreach($filters as $filter) {
//            if($filter instanceof TB_Form_Filter) {
//                $this->addDeserializationFilter($filter);
//            }
//        }
//        return $this;
//    }

    /**
     * Render the element in the context of a chain of parent elements.
     * Should return a single TB_DOM_Tag instance holding all of the markup.
     *
     * Call resetDecorators() between each call to render().
     * This method *should not* output content to the browser.
     *
     * @param array $parents
     * @return DOM
     */
    abstract public function render(array $parents = []);

    /**
     * Get the element value in a normalized format.
     * Note that some elements may use a different format internally (getValue), this will be in the format those
     * elements decide to expose it to the public.
     *
     * @return mixed|null
     */
    public function getData()
    {
        return $this->getValue();
    }

    /**
     * Get the element value in a serialized, scalar format after all registered serialization filters have been applied.
     * Use this if you need to store the value in the database.
     *
     * @return mixed
     */
    public function getSerializedData()
    {
        return $this->applyFilters($this->getValue(), self::FILTER_MODE_SERIALIZE);
    }

    /**
     * Set the element value in a normalized format.
     * Note that some elements may use a different format internally (setValue), this should be set in the format those
     * elements decide to expose it to the public.
     *
     * @param mixed $value
     * @return $this
     */
    public function setData($value)
    {
        return $this->setValue($value);
    }

    /**
     * Set the element value in a serialized, scalar format usually when coming from the request or a database.
     * This method will relay the value to setData() after all registered deserialization filters have been applied.
     *
     * @param mixed $value
     * @return $this
     */
    public function setSerializedData($value)
    {
        return $this->setValue($this->applyFilters($value, self::FILTER_MODE_DESERIALIZE));
    }

    /**
     * Supply default element options.
     * Not to be confused with the element's default value which is part of this structure ( array('default => ...) ).
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [];
    }

    /**
     * Get the element's internal value if set or a default one.
     * If a default value isn't set either, null is returned.
     *
     * @return mixed|null
     */
    protected function getValue()
    {
        if ($this->valueIsset) {
            return $this->value;
        }
        if ($this->hasOption('default') || $this->hasDefaultOption('default')) {
            return $this->getOption('default');
        }
        return null;
    }


    /**
     * Set the element's internal value.
     *
     * @param mixed $value
     * @return $this
     */
    protected function setValue($value)
    {
        $this->valueIsset = true;
        $this->value = $value;
        return $this;
    }

    /**
     * Apply element decorators onto the final output.
     * The caller should be the name of the form element's class which is triggering the decoration.
     *
     * Always call decorate() with the current element's class name even if you are supplying an already decorated
     * (by the extended element) content.
     *
     * Call resetDecorators() between each call to render().
     *
     * @param string $callerClassName
     * @param DOM $render
     * @param array $parents
     * @return mixed|DOM
     */
    protected function decorate($callerClassName, DOM $render, array $parents = [])
    {
        // don't trigger inherited decorators
        if (get_class($this) !== $callerClassName) {
            return $render;
        }

        /** @var DecoratorAbstract $decorator */
        foreach($this->getDecorators() as $decorator) {
            // clone the render tag to avoid incorrect modification in decorators
            $render = $decorator->decorate(clone $render, $this, $parents);
        }

        return $render;
    }

    /**
     * Element constructor.
     *
     * @param string $name
     * @param array $options
     * @throws \Exception
     */
    public function __construct($name, array $options = [])
    {
        if (
            !preg_match('/^[' . self::NAME_REGEX_RANGE . ']+$/', $name) &&
            !$this instanceof Form // a form can be the only nameless form element
        ) {
            throw new \Exception('Invalid form element name: "' . $name . '"' .
                                ', the name can include only symbols in the following range: ' . self::NAME_REGEX_RANGE);
        }

        $this->setName($name);
        $this->setOptions($options);

        if (isset($options['filters'])) {
            if (
                isset($options['filters'][self::FILTER_MODE_SERIALIZE]) &&
                is_array($options['filters'][self::FILTER_MODE_SERIALIZE])
            ) {
                $this->addSerializationFilters($options['filters'][self::FILTER_MODE_SERIALIZE]);
            }
            if (
                isset($options['filters'][self::FILTER_MODE_DESERIALIZE]) &&
                is_array($options['filters'][self::FILTER_MODE_DESERIALIZE])
            ) {
                $this->addDeserializationFilters($options['filters'][self::FILTER_MODE_DESERIALIZE]);
            }
        }
    }

    /**
     * Render shortcut.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->render();
    }

    /**
     * Get the actual HTML compliant name of the element in the context of a chain of parent elements.
     *
     * @param array $parents
     * @return string
     */
    public function getNameForRendering(array $parents = [])
    {
        $trail = [];

        /** @var ElementAbstract $parent */
        foreach ($parents as $parent) {
            if (0 < strlen($parent->getName())) {
                $trail[] = $parent->getName();
            }
        }

        $trail[] = $this->getName();
        return array_shift($trail) . (empty($trail) ? '' : '[' . implode('][', $trail) . ']');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasDefaultOption($name)
    {
        return array_key_exists($name, $this->getDefaultOptions());
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function getDefaultOption($name)
    {
        if (!$this->hasDefaultOption($name)) {
            throw new \Exception('Element has no default option with name: ' . $name . '. Override getDefaultOptions().');
        }
        $defaults = $this->getDefaultOptions();
        return $defaults[$name];
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function getOption($name)
    {
        if ($this->hasOption($name)) {
            return $this->options[$name];
        }

        if ($this->hasDefaultOption($name)) {
            return $this->getDefaultOption($name);
        }

        throw new \Exception('Cannot get option: ' . $name .
                            ', option is not set and no default value is declared. Override getDefaultOptions().');
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getDecorators()
    {
        return $this->decorators;
    }

    /**
     * @param array $decorators
     * @return $this
     */
    public function setDecorators(array $decorators)
    {
        $this->decorators = $decorators;
        return $this;
    }

    /**
     * @param DecoratorAbstract $decorator
     * @return $this
     */
    public function addDecorator(DecoratorAbstract $decorator)
    {
        $this->decorators[] = $decorator;
        return $this;
    }

    /**
     * @return $this
     */
    public function resetDecorators()
    {
        /** @var DecoratorAbstract $decorator */
        foreach ($this->decorators as $decorator) {
            $decorator->reset();
        }
        return $this;
    }
}