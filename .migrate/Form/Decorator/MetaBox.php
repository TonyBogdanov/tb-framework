<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Decorator_MetaBox
 *
 * Decorator for displaying a form as a meta box.
 */
class TB_Form_Decorator_MetaBox extends TB_Form_Decorator
{
    /**
     * @inheritDoc
     */
    public function decorate(TB_DOM_Tag $render, TB_Form_Element $element, array $parents = array())
    {
        if(!$this->canDecorate($element, $parents)) {
            return $render;
        }

        // visibility service
        /** @var TB_Visibility $visibility */
        $visibility = $this->sm('visibility');

        // top classes (which extend from another class) must be first or they'll never be reached
        switch(true) {

            // meta box form are part of the main post / page / comment etc. form, no need for the form tag
            case $element instanceof TB_Form_Form:
                $render = TB_DOM_Tag::nlInnerAfter('table', false, array(
                    'class' => 'form-table'
                ))->setIndentation(1)->append(
                    TB_DOM_Tag::nlInnerAfter('tbody')->setIndentation(1)->append(
                        $form = $render
                    )->unwrap($form)
                );

                if($element->hasOption('visible')) {
                    /** @var TB_Visibility_Expression $expression */
                    $expression = $element->getOption('visible');
                    $expression->bind($render, $parents);
                }

                $visibility->registerScope($render);

                return $render;

            // just ignore fieldsets
            case $element instanceof TB_Form_Element_Fieldset:
                if($element->hasOption('visible')) {
                    /** @var TB_Visibility_Expression $expression */
                    $expression = $element->getOption('visible');
                    $expression->bind($render, $parents);
                }

                return $render;

            // elements
            case $element instanceof TB_Form_Element:
                $tr = TB_DOM_Tag::nlInnerAfter('tr')->setIndentation(1);
                $th = TB_DOM_Tag::nlInnerAfter('td', false, array(
                    'style' => 'width:240px;vertical-align:top'
                ))->setIndentation(1);
                $td = TB_DOM_Tag::nlInnerAfter('td', false, array(
                    'style' => 'vertical-align:top'
                ))->setIndentation(1);
                $span = TB_DOM_Tag::nlAfter('strong')->text($element->getOption('title'));

                if($element->hasOption('description')) {
                    $th->append(TB_DOM_Tag::nlInnerAfter('p', false, array(
                        'class' => 'description'
                    ))->html($element->getOption('description'))->setIndentation(1));
                }

                $th->prepend($span);
                $td->append($render);
                $tr->append($th);
                $tr->append($td);

                if($element->hasOption('visible')) {
                    /** @var TB_Visibility_Expression $expression */
                    $expression = $element->getOption('visible');
                    $expression->bind($tr, $parents);
                }

                return $tr;
        }

        return $render;
    }
}