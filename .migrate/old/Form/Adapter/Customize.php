<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Adapter_Customize
 */
class TB_Form_Adapter_Customize extends WP_Customize_Control
{
    /**
     * @var TB_Form_Element
     */
    protected $element;

    /**
     * TB_Form_Adapter_Customize constructor.
     *
     * @param TB_Form_Element $element
     * @param WP_Customize_Manager $manager
     * @param array $id
     * @param array $args
     */
    public function __construct(TB_Form_Element $element, WP_Customize_Manager $manager, $id, $args = array())
    {
        parent::__construct($manager, $id, $args);
        $this->setElement($element);
    }

    /**
     * @inheritDoc
     */
    protected function render_content()
    {
        $container = TB_DOM_Tag::nlNone('div');
        $label = TB_DOM_Tag::nlInnerAfter('label')->setIndentation(1);

        if(!empty($this->label)) {
            $label->append(TB_DOM_Tag::nlAfter('span', false, array(
                'class' => 'customize-control-title'
            ))->text($this->label));
        }

        if(!empty($this->description)) {
            $label->append(TB_DOM_Tag::nlAfter('span', false, array(
                'class' => 'description customize-control-description'
            ))->html($this->description));
        }

        $element = $this->getElement()->setSerializedData($this->value());
        $render = $element->render();
        $input = TB_Form_Element::findInputElement($render);

        if(!$input instanceof TB_DOM_Tag) {
            throw new Exception('Could not adapt element ' . get_class($element) . ', could not find base' .
                                ' <input> data carrier element.');
        }

        if(isset($this->settings['default'])) {
            $input->setAttribute('data-customize-setting-link', $this->settings['default']->id);
        }

        $container->append($label);
        $container->append($render);

        echo $container->html();
    }

    /**
     * @return TB_Form_Element
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param TB_Form_Element $element
     *
     * @return TB_Form_Adapter_Customize
     */
    public function setElement($element)
    {
        $this->element = $element;
        return $this;
    }
}