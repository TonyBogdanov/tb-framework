<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Filter_Collection
 */
abstract class TB_Form_Filter_Collection extends TB_Form_Filter implements TB_Initializable
{
    /**
     * Child filters to be run.
     * {array}
     */
    const CONFIG_FILTERS = 'filters';

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->ownConfig()->depend(self::CONFIG_FILTERS);
    }

    /**
     * @inheritDoc
     */
    public function filter($value, TB_Form_Element $element)
    {
        /** @var TB_Form_Filter $filter */
        foreach($this->ownConfig(self::CONFIG_FILTERS) as $filter) {
            $value = $filter->filter($value, $element);
        }
        return $value;
    }
}