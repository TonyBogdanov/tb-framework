<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Image_Transformation_ResizeCover
 */
class TB_Image_Transformation_ResizeCover extends TB_Image_Transformation_ResizeContain
{
    const ALIGN_HORIZONTAL_LEFT     = 'left';
    const ALIGN_HORIZONTAL_CENTER   = 'center';
    const ALIGN_HORIZONTAL_RIGHT    = 'right';

    const ALIGN_VERTICAL_TOP        = 'top';
    const ALIGN_VERTICAL_CENTER     = 'center';
    const ALIGN_VERTICAL_BOTTOM     = 'bottom';

    /**
     * @var string
     */
    protected $alignHorizontal;

    /**
     * @var string
     */
    protected $alignVertical;

    /**
     * TB_Image_Transformation_ResizeCover constructor.
     *
     * @param int $width
     * @param int $height
     * @param string $alignHorizontal
     * @param string $alignVertical
     */
    public function __construct(
        $width,
        $height,
        $alignHorizontal = TB_Image_Transformation_ResizeCover::ALIGN_HORIZONTAL_CENTER,
        $alignVertical = TB_Image_Transformation_ResizeCover::ALIGN_VERTICAL_CENTER
    ) {
        parent::__construct($width, $height);

        $this->setAlignHorizontal($alignHorizontal);
        $this->setAlignVertical($alignVertical);
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize(array(
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
            'alignHorizontal' => $this->getAlignHorizontal(),
            'alignVertical' => $this->getAlignVertical()
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
        $this->setAlignHorizontal($serialized['alignHorizontal']);
        $this->setAlignVertical($serialized['alignVertical']);
    }

    /**
     * @inheritDoc
     */
    public function apply(TB_Image_Image $image)
    {
        $targetWidth = $this->normalize($image, $this->getWidth());
        $targetHeight = $this->normalize($image, $this->getHeight());

        if(!isset($targetWidth) || !isset($targetHeight)) {
            throw new Exception('Both dimensions must be specified.');
        }

        $originalSize = $image->getEditor()->get_size();

        $newWidth = min($targetWidth, $originalSize['width']);
        $newHeight = min($targetHeight, $originalSize['height']);

        $sizeRatio = max($newWidth / $originalSize['width'], $newHeight / $originalSize['height']);

        $cropWidth = (int) round($newWidth / $sizeRatio);
        $cropHeight = (int) round($newHeight / $sizeRatio);

        switch($this->getAlignHorizontal()) {
            case self::ALIGN_HORIZONTAL_CENTER:
                $offsetX = (int) round(($originalSize['width'] - $cropWidth) / 2);
                break;

            case self::ALIGN_HORIZONTAL_RIGHT:
                $offsetX = $originalSize['width'] - $cropWidth;
                break;

            default:
                $offsetX = 0;
        }

        switch($this->getAlignVertical()) {
            case self::ALIGN_VERTICAL_CENTER:
                $offsetY = (int) round(($originalSize['height'] - $cropHeight) / 2);
                break;

            case self::ALIGN_VERTICAL_BOTTOM:
                $offsetY = $originalSize['height'] - $cropHeight;
                break;

            default:
                $offsetY = 0;
        }

        $result = $image->getEditor()->crop($offsetX, $offsetY, $cropWidth, $cropHeight, $targetWidth, $targetHeight);
        if(is_wp_error($result)) {
            throw new Exception($result->get_error_message());
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getAlignHorizontal()
    {
        return $this->alignHorizontal;
    }

    /**
     * @param string $alignHorizontal
     *
     * @return $this
     */
    public function setAlignHorizontal($alignHorizontal)
    {
        $this->alignHorizontal = $alignHorizontal;
        return $this;
    }

    /**
     * @return string
     */
    public function getAlignVertical()
    {
        return $this->alignVertical;
    }

    /**
     * @param string $alignVertical
     *
     * @return $this
     */
    public function setAlignVertical($alignVertical)
    {
        $this->alignVertical = $alignVertical;
        return $this;
    }
}