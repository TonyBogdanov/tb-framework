<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Element_Fieldset
 */
class TB_Form_Element_Fieldset extends TB_Form_Element
{
    /**
     * @var array
     */
    protected $elements = array();

    /**
     * TB_Form_Element_Fieldset constructor.
     *
     * @param $name
     * @param array $options
     * @param array $elements
     */
    public function __construct($name, array $options = array(), array $elements = array())
    {
        parent::__construct($name, $options);

        foreach($elements as $element) {
            if(is_array($element)) {
                $this->addElement($element[1], $element[0]);
            } else {
                $this->addElement($element);
            }
        }
    }

    /**
     * @param TB_Form_Element $element
     * @param int $priority
     *
     * @return $this
     */
    public function addElement(TB_Form_Element $element, $priority = 0)
    {
        if(!isset($this->elements[$priority])) {
            $this->elements[$priority] = array();
        }
        $this->elements[$priority][] = $element;
        return $this->sortElements();
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @return array
     */
    public function getElementsWithoutPriorities()
    {
        $result = array();

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
     * @param $elements
     *
     * @return $this
     */
    public function addElements($elements)
    {
        /** @var TB_Form_Element $element */
        foreach($elements as $element) {
            $this->addElement($element);
        }
        return $this->sortElements();
    }

    /**
     * @return bool
     */
    public function hasElements()
    {
        return 0 < count($this->elements);
    }

    /**
     * @return $this
     */
    public function sortElements()
    {
        ksort($this->elements);
        return $this;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasElement($name)
    {
        return false !== $this->getElement($name);
    }

    /**
     * @param $name
     *
     * @return bool|TB_Form_Element
     */
    public function getElement($name)
    {
        /** @var TB_Form_Element $element */
        foreach($this->getElementsWithoutPriorities() as $element) {
            if($element->getName() === $name) {
                return $element;
            }
        }
        return false;
    }

    /**
     * @param TB_Form_Decorator $decorator
     *
     * @return $this
     */
    public function addDecoratorDeep(TB_Form_Decorator $decorator)
    {
        /** @var TB_Form_Element $element */
        foreach($this->getElementsWithoutPriorities() as $element) {
            if($element instanceof self) {
                $element->addDecoratorDeep($decorator);
            } else {
                $element->addDecorator($decorator);
            }
        }
        return $this->addDecorator($decorator);
    }

    /**
     * @return $this
     */
    public function resetDecoratorsDeep()
    {
        /** @var TB_Form_Element $element */
        foreach($this->getElementsWithoutPriorities() as $element) {
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
        $data = array();

        /** @var TB_Form_Element $element */
        foreach($this->getElementsWithoutPriorities() as $element) {
            $data[$element->getName()] = $element->getData();
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getSerializedData()
    {
        $data = array();

        /** @var TB_Form_Element $element */
        foreach($this->getElementsWithoutPriorities() as $element) {
            $data[$element->getName()] = $element->getSerializedData();
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function setData($value)
    {
        if(!is_array($value) || empty($value)) {
            return $this;
        }

        /** @var TB_Form_Element $element */
        foreach($this->getElementsWithoutPriorities() as $element) {
            if(array_key_exists($element->getName(), $value)) {
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

        /** @var TB_Form_Element $element */
        foreach($this->getElementsWithoutPriorities() as $element) {
            if(array_key_exists($element->getName(), $value)) {
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
    public function render(array $parents = array())
    {
        $render = new TB_DOM_Tag();
        if(!$this->hasElements()) {
            return $render;
        }

        $parents[] = $this;

        /** @var TB_Form_Element $element */
        foreach($this->getElementsWithoutPriorities() as $element) {
            $render->append($element->render($parents));
        }

        return $this->decorate(__CLASS__, $render, $parents);
    }
}