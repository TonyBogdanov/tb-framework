<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Form\Element;

/**
 * Color form control.
 *
 * Class Color
 * @package TB\Form\Element
 */
class Color
{
    /**
     * @inheritDoc
     */
    protected function getDefaultSerializationFilters()
    {
        throw new \Exception(__METHOD__);
        return array(
            new TB_Form_Filter_ColorToCSSHexWithoutOpacity()
        );
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultDeserializationFilters()
    {
        throw new \Exception(__METHOD__);
        return array(
            new TB_Form_Filter_Color()
        );
    }

    /**
     * @inheritDoc
     */
    public function render(array $parents = array())
    {
        return $this->decorate(
            __CLASS__,
            parent::render($parents)
                ->addClass('tb-color-picker')
                ->attr('data-default', $this->getOption('default')),
            $parents
        );
    }
}