<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Util_Color
 */
class TB_Util_Color
{
    /**
     * @var int
     */
    protected $color = 0;

    /**
     * @param $value
     *
     * @return bool
     */
    protected function fromNumeric($value)
    {
        if(!is_numeric($value) || 0 > $value || 0xffffffff < $value) {
            return false;
        }

        $this->color = $value;
        return true;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    protected function fromHex($value)
    {
        if(!TB_Util::canBeCastToString($value)) {
            return false;
        }

        $value = preg_replace('/[^0-9a-f]+/', '', strtolower((string) $value));

        if(6 == strlen($value)) {
            $value .= 'ff';
        } else if(3 == strlen($value)) {
            $value = $value[0] . $value[0] . $value[1] . $value[1] . $value[2] . $value[2];
        } else if(4 == strlen($value)) {
            $value = $value[0] . $value[0] . $value[1] . $value[1] . $value[2] . $value[2] . $value[3] . $value[3];
        } else if(8 != strlen($value)) {
            return false;
        }

        $this->color = hexdec($value);
        return true;
    }

    /**
     * TB_Util_Color constructor.
     *
     * @param int $color
     *
     * @throws Exception
     */
    public function __construct($color = 0)
    {
        if($color instanceof self) {
            $this->color = $color->toNumeric();
        } else if(!(
            $this->fromNumeric($color) ||
            $this->fromHex($color)
        )) {
            throw new Exception('Unrecognized color expression');
        }
    }

    /**
     * @return int
     */
    public function toNumeric()
    {
        return $this->color;
    }

    /**
     * @return string
     */
    public function toHex()
    {
        return str_pad(dechex($this->color), 8, 'f');
    }
}