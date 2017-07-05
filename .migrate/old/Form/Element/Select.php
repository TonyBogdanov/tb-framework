<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Element_Select
 */
class TB_Form_Element_Select extends TB_Form_Element
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
        $render = TB_DOM_Tag::nlInnerAfter('select', false, array(
            'name' => $this->getNameForRendering($parents)
        ))->setIndentation(1);

        $valueOptions = $this->getOption('value_options');
        if(!is_array($valueOptions)) {
            $valueOptions = array();
        }

        if(empty($valueOptions)) {
            $valueOptions[''] = '--- Empty ---';
        }

        foreach($valueOptions as $value => $label) {
            $render->append(TB_DOM_Tag::nlAfter('option', false, array_merge(array(
                'value' => (string) $value
            ), $this->getValue() === (string) $value ? array(
                'selected' => 'selected'
            ) : array()))->text((string) $label));
        }

        return $this->decorate(__CLASS__, $render, $parents);
    }
}