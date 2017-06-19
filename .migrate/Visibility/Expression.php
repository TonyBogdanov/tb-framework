<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Visibility_Expression
 *
 * Represents a visibility expression to determine whether to show or hide a bound object.
 */
abstract class TB_Visibility_Expression extends TB_ServiceManager_Aware
{
    /**
     * Get the string representation of the expression to be used when processing with JavaScript.
     *
     * @param null $context
     * @return mixed
     */
    abstract public function getNotation($context = null);

    /**
     * Bind the expression to a DOM object.
     * Whenever the condition result of the expression changes, so will the visibility of the bound object.
     *
     * Optionally you can pass context of any type to be used during notation generation.
     *
     * @param TB_DOM_Tag $tag
     * @param mixed $context
     * @return $this
     */
    public function bind(TB_DOM_Tag $tag, $context = null)
    {
        $tag->setAttribute('data-visibility-expression', $this->getNotation($context));
        return $this;
    }
}