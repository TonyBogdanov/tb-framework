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

/**
 * Decorator for displaying a form as an admin page.
 *
 * Class AdminPage
 * @package TB\Form\Decorator
 */
class AdminPage extends DecoratorAbstract
{
    /**
     * @inheritDoc
     */
    public function decorate(DOM $render, ElementAbstract $element, array $parents = [])
    {
        throw new \Exception(__METHOD__);

        if(!$this->canDecorate($element, $parents)) {
            return $render;
        }

        // visibility service
        /** @var TB_Visibility $visibility */
        $visibility = $this->sm('visibility');

        // top classes (which extend from another class) must be first or they'll never be reached
        switch(true) {
            case $element instanceof TB_Form_Form:
                // children are fieldsets (render as tabs)?
                $childrenTypes = array();
                $childFieldset = false;

                /**
                 * @var int $priority
                 * @var array $elements
                 */
                foreach($element->getElements() as $priority => $elements) {
                    /** @var TB_Form_Element $subElement */
                    foreach($elements as $subElement) {
                        if($subElement instanceof TB_Form_Element_Fieldset) {
                            $childFieldset = true;
                        }
                        $childrenTypes[] = get_class($subElement);
                    }
                }

                $childrenTypes = array_unique($childrenTypes);

                if($childFieldset) {
                    if(1 < count(array_unique($childrenTypes))) {
                        throw new Exception('Cannot decorate, form must either have only fieldsets as immediate' .
                                            ' children or only other types of elements.');
                    }

                    // all fieldsets, render in tabs
                    $headingWrapper = TB_DOM_Tag::nlInnerAfter('h2', false, array(
                        'id' => $element->getOption('id'),
                        'class' => 'nav-tab-wrapper'
                    ))->setIndentation(1);

                    // determine active tab
                    $cookie = 'tb-tabs-' . $element->getOption('id');
                    $activeTab = isset($_COOKIE[$cookie]) ? (int) $_COOKIE[$cookie] : 0;

                    /** @var TB_DOM_Tag $fieldset */
                    foreach($render->getChildren() as $index => $fieldset) {
                        $heading = current($fieldset->getChildren());
                        $fieldset->remove($heading);

                        $nav = TB_DOM_Tag::nlAfter('a', false, array(
                            'href' => '#tb_tab_' . $fieldset->getAttribute('data-fieldset'),
                            'class' => 'nav-tab' . ($index == $activeTab ? ' nav-tab-active' : '')
                        ))->text($heading->text());
                        $headingWrapper->append($nav);

                        $fieldset
                            ->setAttribute('id', 'tb_tab_' . $fieldset->getAttribute('data-fieldset'))
                            ->removeAttribute('data-fieldset');

                        if($index == $activeTab) {
                            $fieldset->setAttribute('class', 'tb-tab-active');
                        }
                    }

                    $render->prepend($headingWrapper);
                    $render->append(TB_DOM_Tag::nlAfter('br', true), $headingWrapper);
                } else {
                    // no fieldsets, don't render tabs
                    /** @var TB_DOM_Tag $fieldset */
                    foreach($render->getChildren() as $fieldset) {
                        $render->unwrap($fieldset->append(TB_DOM_Tag::nlAfter('p')->html('&nbsp;')));
                    }
                }

                $render->setIndentation(1);

                if($element->hasOption('visible')) {
                    /** @var TB_Visibility_Expression $expression */
                    $expression = $element->getOption('visible');
                    $expression->bind($render, $parents);
                }

                $visibility->registerScope($render);

                return $render;

            case $element instanceof TB_Form_Element_Fieldset:
                $fieldset = TB_DOM_Tag::nlInnerAfter('div', false, array(
                    'data-fieldset' => $element->getName()
                ))->setIndentation(1);

                $table = TB_DOM_Tag::nlInnerAfter('table', false, array(
                    'class' => 'form-table'
                )); // no indentation, thead / tbody / tfoot elements are on the same level

                $tbody = TB_DOM_Tag::nlInnerAfter('tbody', false)->setIndentation(1);

                $heading = TB_DOM_Tag::nlAfter('h3', false, array(
                    'class' => 'container-title'
                ))->text($element->getOption('title'));

                $tbody->append($render);
                $table->append($tbody);
                $fieldset->append($heading)->append($table);

                if($element->hasOption('visible')) {
                    /** @var TB_Visibility_Expression $expression */
                    $expression = $element->getOption('visible');
                    $expression->bind($fieldset, $parents);
                }

                return $fieldset;

            case $element instanceof TB_Form_Element:
                $tr = TB_DOM_Tag::nlInnerAfter('tr')->setIndentation(1);
                $th = TB_DOM_Tag::nlInnerAfter('th', false, array(
                    'style' => 'vertical-align:top'
                ))->setIndentation(1);
                $td = TB_DOM_Tag::nlInnerAfter('td', false, array(
                    'style' => 'vertical-align:top'
                ))->setIndentation(1);
                $span = TB_DOM_Tag::nlAfter('span')->text($element->getOption('title'));

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