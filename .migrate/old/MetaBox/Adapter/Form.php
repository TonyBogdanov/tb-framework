<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_MetaBox_Adapter_Form
 */
class TB_MetaBox_Adapter_Form extends TB_MetaBox_MetaBox implements TB_Initializable
{
    /**
     * Prefix form elements when rendered in the context of a post or page form.
     */
    const NAME_PREFIX = 'mbf_';

    /**
     * Reference to the bound form.
     *
     * @var TB_Form_Form
     */
    protected $form;

    /**
     * TB_MetaBox_Adapter_Form constructor.
     *
     * @param string $title
     * @param TB_Form_Form $form
     */
    public function __construct($title, TB_Form_Form $form)
    {
        parent::__construct($title);
        $this->setForm($form);
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        /** @var TB_Form_Decorator_MetaBox $decorator */
        $decorator = $this->sm()->create('form.decorator.meta_box');

        $this->getForm()
             ->setName(self::NAME_PREFIX . $this->getId())
             ->addDecoratorDeep($decorator);
    }

    /**
     * @inheritDoc
     */
    public function render(WP_Post $post)
    {
        $form = $this->getForm();
        $data = get_post_meta($post->ID, $form->getName(), true);

        if(is_array($data)) {
            $form->setSerializedData($data);
        }

        echo $form->render();
    }

    /**
     * @inheritDoc
     */
    public function save(WP_Post $post)
    {
        $form = $this->getForm();
        if(
            isset($_SERVER['REQUEST_METHOD']) &&
            'POST' == $_SERVER['REQUEST_METHOD'] &&
            array_key_exists($key = $form->getName(), $_POST)
        ) {
            $form->setSerializedData(stripslashes_deep($_POST[$key]));
            if($form->isValid()) {
                update_post_meta($post->ID, $key, $form->getSerializedData());
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getRenderCallback()
    {
        throw new Exception('Calls to ' . __METHOD__ . ' are not permitted.');
    }

    /**
     * @inheritDoc
     */
    public function getSaveCallback()
    {
        throw new Exception('Calls to ' . __METHOD__ . ' are not permitted.');
    }

    /**
     * @inheritDoc
     */
    public function setRenderCallback($renderCallback)
    {
        throw new Exception('Calls to ' . __METHOD__ . ' are not permitted.');
    }

    /**
     * @inheritDoc
     */
    public function setSaveCallback($saveCallback)
    {
        throw new Exception('Calls to ' . __METHOD__ . ' are not permitted.');
    }

    /**
     * @return TB_Form_Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param TB_Form_Form $form
     * @return TB_MetaBox_Adapter_Form
     */
    public function setForm($form)
    {
        $this->form = $form;
        return $this;
    }
}