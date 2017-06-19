<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Element_Text
 */
class TB_Form_Element_Text extends TB_Form_Element
{
    /**
     * @inheritDoc
     */
    protected function getDefaultSerializationFilters()
    {
        return array(
            $this->sm()->create('form.filter.string')
        );
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultDeserializationFilters()
    {
        return array(
            $this->sm()->create('form.filter.string')
        );
    }

    /**
     * @inheritDoc
     */
    public function render(array $parents = array())
    {
        $render = TB_DOM_Tag::nlAfter('input', true, array(
            'type' => 'text',
            'name' => $this->getNameForRendering($parents),
            'value' => $this->getSerializedData()
        ));
        return $this->decorate(__CLASS__, $render, $parents);
    }
}