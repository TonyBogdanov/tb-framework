<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Form;

use TB\DOM\DOM;
use TB\Form\Decorator\DecoratorAbstract;
use TB\Form\Element\ElementAbstract;

/**
 * A fieldset element - a collection of form elements.
 *
 * Class Fieldset
 * @package TB\Form
 */
class Fieldset extends ElementAbstract
{
    /**
     * Child elements holder.
     *
     * @var array
     */
    protected $elements = [];

    /**
     * Fieldset constructor.
     *
     * @param string $name
     * @param array $options
     * @param array $elements
     */
    public function __construct($name, array $options = [], array $elements = [])
    {
        parent::__construct($name, $options);

        foreach ($elements as $element) {
            if (is_array($element)) {
                $this->addElement($element[1], $element[0]);
            } else {
                $this->addElement($element);
            }
        }
    }

    /**
     * Add an element to the collection with the specified priority.
     *
     * @param ElementAbstract $element
     * @param int $priority
     * @return $this
     */
    public function addElement(ElementAbstract $element, $priority = 0)
    {
        if (!isset($this->elements[$priority])) {
            $this->elements[$priority] = [];
        }
        $this->elements[$priority][] = $element;
        return $this->sortElements();
    }

    /**
     * Retrieve all elements in a linear list (strip priority grouping).
     *
     * @return array
     */
    public function getElementsWithoutPriorities()
    {
        $result = [];

        /**
         * @var int $priority
         * @var array $elements
         */
        foreach($this->getElements() as $priority => $elements) {
            $result = array_merge($result, $elements);
        }

        return $result;
    }

    /**
     * Get all elements.
     *
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Add a collection of elements.
     *
     * @param array $elements
     * @return $this
     */
    public function addElements(array $elements)
    {
        /** @var ElementAbstract $element */
        foreach($elements as $element) {
            $this->addElement($element);
        }
        return $this->sortElements();
    }

    /**
     * Does the current fieldset has any elements.
     *
     * @return bool
     */
    public function hasElements()
    {
        return 0 < count($this->elements);
    }

    /**
     * Sort all elements based on their priorities.
     *
     * @return $this
     */
    public function sortElements()
    {
        ksort($this->elements);
        return $this;
    }

    /**
     * Determines whether an element with the specified name is added to the collection.
     *
     * @param string $name
     * @return bool
     */
    public function hasElement($name)
    {
        return false !== $this->getElement($name);
    }

    /**
     * Retrieve an element from the collection with the specified name.
     * Returns FALSE if the element does not exist.
     *
     * @param string $name
     * @return bool|ElementAbstract
     */
    public function getElement($name)
    {
        /** @var ElementAbstract $element */
        foreach($this->getElementsWithoutPriorities() as $element) {
            if($element->getName() === $name) {
                return $element;
            }
        }
        return false;
    }

    /**
     * Recursively adds a decorator to all elements in the collection.
     * If any of the elements are collections themselves, they will also recurse the decorator to their children.
     *
     * @param DecoratorAbstract $decorator
     * @return $this
     */
    public function addDecoratorDeep(DecoratorAbstract $decorator)
    {
        /** @var ElementAbstract $element */
        foreach ($this->getElementsWithoutPriorities() as $element) {
            if($element instanceof self) {
                $element->addDecoratorDeep($decorator);
            } else {
                $element->addDecorator($decorator);
            }
        }
        return $this->addDecorator($decorator);
    }

    /**
     * Resets decorator statistics for all elements in the collection.
     * If any of the elements are collections themselves, they will also recurse the reset to their children.
     *
     * @return $this
     */
    public function resetDecoratorsDeep()
    {
        /** @var ElementAbstract $element */
        foreach ($this->getElementsWithoutPriorities() as $element) {
            if($element instanceof self) {
                $element->resetDecoratorsDeep();
            } else {
                $element->resetDecorators();
            }
        }
        return $this->resetDecorators();
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $data = [];

        /** @var ElementAbstract $element */
        foreach ($this->getElementsWithoutPriorities() as $element) {
            $data[$element->getName()] = $element->getData();
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getSerializedData()
    {
        $data = [];

        /** @var ElementAbstract $element */
        foreach ($this->getElementsWithoutPriorities() as $element) {
            $data[$element->getName()] = $element->getSerializedData();
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function setData($value)
    {
        if (!is_array($value) || empty($value)) {
            return $this;
        }

        /** @var ElementAbstract $element */
        foreach ($this->getElementsWithoutPriorities() as $element) {
            if (array_key_exists($element->getName(), $value)) {
                $element->setData($value[$element->getName()]);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSerializedData($value)
    {
        if(!is_array($value) || empty($value)) {
            return $this;
        }

        /** @var ElementAbstract $element */
        foreach ($this->getElementsWithoutPriorities() as $element) {
            if (array_key_exists($element->getName(), $value)) {
                $element->setSerializedData($value[$element->getName()]);
            }
        }

        return $this;
    }

    // todo validation
    public function isValid()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function render(array $parents = [])
    {
        $render = new DOM();
        if (!$this->hasElements()) {
            return $render;
        }

        $parents[] = $this;

        /** @var ElementAbstract $element */
        foreach ($this->getElementsWithoutPriorities() as $element) {
            $render->append($element->render($parents));
        }

        return $this->decorate(__CLASS__, $render, $parents);
    }
}