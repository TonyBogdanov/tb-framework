<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_MetaBox
 *
 * Meta box service.
 */
class TB_MetaBox extends TB_ServiceManager_Aware
{
    /**
     * @var array
     */
    protected $boxes = array();

    /**
     * TB_MetaBox constructor.
     */
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, '__actionAddMetaBoxes'));
        add_action('save_post', array($this, '__actionSavePost'));
    }

    /**
     * Register a meta box.
     * Based on it's options it will be handled accordingly.
     *
     * Returns the meta box instance for further configuration.
     *
     * @param TB_MetaBox_MetaBox $metaBox
     * @return TB_MetaBox_MetaBox
     */
    public function register(TB_MetaBox_MetaBox $metaBox)
    {
        $this->boxes[] = $metaBox;
        return $metaBox;
    }

    /**
     * Register a meta box automatically generated and bound to the specified form object.
     *
     * Returns the meta box instance for further configuration.
     *
     * @param string $title
     * @param TB_Form_Form $form
     * @return TB_MetaBox_MetaBox
     */
    public function registerForm($title, TB_Form_Form $form)
    {
        /** @var TB_MetaBox_Adapter_Form $adapter */
        $adapter = $this->sm()->create('meta_box.adapter.form', array(
            $title,
            $form
        ));
        return $this->register($adapter);
    }

    /**
     * @hook add_meta_boxes
     */
    public function __actionAddMetaBoxes()
    {
        /** @var TB_MetaBox_MetaBox $metaBox */
        foreach($this->boxes as $metaBox) {
            add_meta_box(
                $metaBox->getId(),
                $metaBox->getTitle(),
                array($metaBox, 'render'),
                $metaBox->getScreen(),
                $metaBox->getContext(),
                $metaBox->getPriority()
            );
        }
        return $this;
    }

    /**
     * @hook save_post
     */
    public function __actionSavePost($pid)
    {
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $pid;
        }

        $post = get_post($pid);
        $currentScreen = get_current_screen();

        /** @var TB_MetaBox_MetaBox $metaBox */
        foreach($this->boxes as $metaBox) {
            // only save on matching screens
            if($metaBox->isActiveOnScreen($currentScreen)) {
                $metaBox->save($post);
            }
        }

        return $this;
    }
}