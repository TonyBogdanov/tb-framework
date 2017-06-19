<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Element_Checkbox
 */
class TB_Form_Element_Checkbox extends TB_Form_Element
{
    /**
     * @inheritDoc
     */
    protected function getDefaultDeserializationFilters()
    {
        return array(
            $this->sm()->create('form.filter.boolean')
        );
    }

    /**
     * @inheritDoc
     */
    public function render(array $parents = array())
    {
        $hidden = TB_DOM_Tag::nlAfter('input', true, array(
            'type' => 'hidden',
            'name' => $this->getNameForRendering($parents),
            'value' => ''
        ));
        $checkbox = TB_DOM_Tag::nlAfter('input', true, array_merge(array(
            'type' => 'checkbox',
            'name' => $this->getNameForRendering($parents),
            'value' => '1'
        ), $this->getValue() ? array(
            'checked' => 'checked'
        ) : array()));

        $render = TB_DOM_Tag::nlInnerAfter('div')->setIndentation(1);
        $render->append($hidden)
               ->append($checkbox);

        return $this->decorate(__CLASS__, $render, $parents);
    }
}