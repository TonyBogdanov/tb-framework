<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Image_Transformation_Export_Height
 */
class TB_Image_Transformation_Export_Height extends TB_Image_Transformation_Export_Size
{
    /**
     * @inheritDoc
     */
    public function apply(TB_Image_Image $image)
    {
        $size = $image->getEditor()->get_size();
        $image->setVariable($this->getVariable(), $size['height']);
        return $this;
    }
}