<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Form\Decorator;

use TB\DOM\DOM;
use TB\Form\Element\ElementAbstract;
use TB\ServiceManager\ServiceManagerAwareInterface;
use TB\ServiceManager\ServiceManagerAwareTrait;

/**
 * Form decorator base class.
 *
 * Class DecoratorAbstract
 * @package TB\Form
 */
abstract class DecoratorAbstract implements
    ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

    /**
     * Holder for already decorated elements references.
     *
     * @var array
     */
    protected $decorated = [];

    /**
     * Decorate the result of calling a form element's render() method for the specified context.
     *
     * @param DOM $render
     * @param ElementAbstract $element
     * @param array $parents
     * @return mixed
     */
    abstract public function decorate(DOM $render, ElementAbstract $element, array $parents = []);

    /**
     * Get the SPL hash of a form element in the context of the current decorator instance.
     *
     * @param ElementAbstract $element
     * @return string
     */
    protected function getHash(ElementAbstract $element)
    {
        return md5(spl_object_hash($this) . ':' . spl_object_hash($element));
    }

    /**
     * Returns TRUE if the specified form element has already been decorated by this decorator instance.
     *
     * @param ElementAbstract $element
     * @return bool
     */
    protected function isDecorated(ElementAbstract $element)
    {
        return in_array($this->getHash($element), $this->decorated);
    }

    /**
     * Sets the specified form element as already decorated by the this decorator instance.
     *
     * @param \TB\Form\Element\ElementAbstract $element
     * @return $this
     */
    protected function setDecorated(ElementAbstract $element)
    {
        $this->decorated[] = $this->getHash($element);
        return $this;
    }

    /**
     * Returns TRUE if the specified form element can be decorated by this decorator in the specified context.
     *
     * @param ElementAbstract $element
     * @param array $parents
     * @return bool
     * @throws \Exception
     */
    protected function canDecorate(ElementAbstract $element, array $parents = [])
    {
        if(!$this->isDecorated($element)) {
            $this->setDecorated($element);
            return true;
        }

        if (!empty($parents)) {
            return false;
        }

        throw new \Exception('Element already decorated, use resetDecorators() if you want to use render()' .
            ' more than once.');
    }

    /**
     * Resets the tracking information about previously decorated form elements.
     *
     * @return $this
     */
    public function reset()
    {
        $this->decorated = array();
        return $this;
    }
}