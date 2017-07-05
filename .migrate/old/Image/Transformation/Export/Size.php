<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Image_Transformation_Export_Size
 */
class TB_Image_Transformation_Export_Size extends TB_Image_Transformation
{
    /**
     * @var string
     */
    protected $variable;

    /**
     * TB_Image_Transformation_Export_Width constructor.
     *
     * @param string $variable
     */
    public function __construct($variable)
    {
        $this->setVariable($variable);
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return $this->getVariable();
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $this->setVariable($serialized);
    }

    /**
     * @inheritDoc
     */
    public function apply(TB_Image_Image $image)
    {
        $image->setVariable($this->getVariable(), $image->getEditor()->get_size());
        return $this;
    }

    /**
     * @return string
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * @param string $variable
     *
     * @return $this
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;
        return $this;
    }
}