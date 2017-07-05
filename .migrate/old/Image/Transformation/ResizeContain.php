<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Image_Transformation_ResizeContain
 */
class TB_Image_Transformation_ResizeContain extends TB_Image_Transformation
{
    /**
     * @var int|null
     */
    protected $width;

    /**
     * @var int|null
     */
    protected $height;

    /**
     * TB_Image_Transformation_Resize constructor.
     *
     * @param int|null $width
     * @param int|null $height
     */
    public function __construct($width = null, $height = null)
    {
        $this->setWidth($width);
        $this->setHeight($height);
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize(array(
            'width' => $this->getWidth(),
            'height' => $this->getHeight()
        ));
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        /** @var array $serialized */
        $serialized = unserialize($serialized);

        $this->setWidth($serialized['width']);
        $this->setHeight($serialized['height']);
    }

    /**
     * @inheritDoc
     */
    public function apply(TB_Image_Image $image)
    {
        $targetWidth = $this->normalize($image, $this->getWidth());
        $targetHeight = $this->normalize($image, $this->getHeight());

        if(!isset($targetWidth) && !isset($targetHeight)) {
            throw new Exception('At least one of the dimensions must be specified.');
        }

        $targetWidth = isset($targetWidth) ? $targetWidth : PHP_INT_MAX;
        $targetHeight = isset($targetHeight) ? $targetHeight : PHP_INT_MAX;

        $originalSize = $image->getEditor()->get_size();
        $ratio = $originalSize['width'] / $originalSize['height'];

        $newWidth = $targetWidth;
        $newHeight = (int) floor($newWidth / $ratio);

        if($newHeight > $targetHeight) {
            $newHeight = $targetHeight;
            $newWidth = (int) floor($newHeight * $ratio);
        }

        $result = $image->getEditor()->crop(0, 0, $originalSize['width'], $originalSize['height'], $newWidth, $newHeight);
        if(is_wp_error($result)) {
            throw new Exception($result->get_error_message());
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int|null $width
     *
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int|null $height
     *
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }
}