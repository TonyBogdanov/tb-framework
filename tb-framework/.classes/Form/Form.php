<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Form;

use TB\DOM\DOM;

/**
 * Form element.
 *
 * Class Form
 * @package TB\Form
 */
class Form extends Fieldset
{
    /**
     * Supported form methods.
     */
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /**
     * Form action (URL).
     *
     * @var string
     */
    protected $action;

    /**
     * The form method (e.g. GET).
     *
     * @var string
     */
    protected $method;

    /**
     * Should the form allow file uploads.
     *
     * @var bool
     */
    protected $upload;

    /**
     * Additional form HTML attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Form constructor.
     *
     * @param string $action
     * @param string $method
     * @param bool $upload
     * @param array $attributes
     * @param array $decorators
     */
    public function __construct(
        $action = '',
        $method = self::METHOD_GET,
        $upload = false,
        array $attributes = [],
        array $decorators = []
    ) {
        parent::__construct('');

        $this->setAction($action);
        $this->setMethod($method);
        $this->setUpload($upload);
        $this->setAttributes($attributes);
        $this->setDecorators($decorators);
    }

    /**
     * @inheritDoc
     */
    public function render(array $parents = [])
    {
        $render = DOM::create('<form/>')->attrs([
            'action' => $this->getAction(),
            'method' => $this->getMethod()
        ]);

        if ($this->isUpload()) {
            $render->attr('enctype', 'multipart/form-data');
        }

        foreach ($this->getAttributes() as $name => $value) {
            $render->attr($name, $value);
        }

        // inherit children from parent fieldset instead of wrapping it
        return $this->decorate(
            __CLASS__,
            0 < parent::render()->count() ? $render->append(parent::render()->children()) : $render,
            $parents
        );
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUpload()
    {
        return $this->upload;
    }

    /**
     * @param bool $upload
     * @return $this
     */
    public function setUpload($upload)
    {
        $this->upload = $upload;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }
}