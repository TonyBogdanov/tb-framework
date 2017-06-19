<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Image_Transformation
 */
abstract class TB_Image_Transformation implements Serializable
{
    /**
     * @param TB_Image_Image $image
     *
     * @return mixed
     */
    abstract public function apply(TB_Image_Image $image);

    /**
     * @param TB_Image_Image $image
     * @param $value
     *
     * @return bool|mixed|null|string
     * @throws Exception
     */
    protected function normalize(TB_Image_Image $image, $value)
    {
        if(is_string($value) && ':' == substr($value, 0, 1)) {
            $value = substr($value, 1);
            if(!$image->hasVariable($value)) {
                throw new Exception('No such variable has been previously exported: ' . $value);
            }
            $value = $image->getVariable($value);
        }
        return $value;
    }
}