<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Visibility_Expression_Value
 *
 * A static value expression.
 */
class TB_Visibility_Expression_Value extends TB_Visibility_Expression
{
    /**
     * Static value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * TB_Visibility_Expression_Value constructor.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->setValue($value);
    }

    /**
     * @inheritDoc
     */
    public function getNotation($context = null)
    {
        $value = (string) $this->getValue();
        return 'value:' . strlen($value) . ':' . $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return TB_Visibility_Expression_Value
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}