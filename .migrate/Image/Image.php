<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Image_Image
 */
class TB_Image_Image
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var WP_Image_Editor
     */
    protected $editor;

    /**
     * @var array
     */
    protected $variables = array();

    /**
     * @var array
     */
    protected $transformations = array();

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->transform();
    }

    /**
     * TB_Image_Image constructor.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->setPath($path);
    }

    /**
     * Apply transformations, cache the result and return the URL.
     *
     * @return string
     * @throws Exception
     */
    public function transform()
    {
        // get file signatures and cache path / uri
        $fileName = TB_Util::crcFile($this->getPath()) . '.' . pathinfo($this->getPath(), PATHINFO_EXTENSION);
        $signature = TB_Util::crc(serialize($this->transformations));
        $cachePath = TB_Util::getCachePath('media-images/' . $signature . '/' . $fileName);
        $cacheUrl = TB_Util::getCacheUri('media-images/' . $signature . '/' . $fileName);

        // todo remove
        TB_Util_Filesystem::delete($cachePath);

        // if cache file already exists, bail early
        if(TB_Util_Filesystem::isFile($cachePath)) {
            return $cacheUrl;
        }

        // prepare the cache dir
        TB_Util_Filesystem::createDirectoryRecursive(dirname($cachePath));

        // init the editor
        $this->setEditor(wp_get_image_editor($this->getPath()));

        // apply transformations
        /** @var TB_Image_Transformation $transformation */
        foreach($this->transformations as $transformation) {
            $transformation->apply($this);
        }

        $result = $this->getEditor()->save($cachePath);
        if(is_wp_error($result)) {
            throw new Exception($result->get_error_message());
        }

        // cleanup & result
        $this->setEditor(null);
        return $cacheUrl;
    }

    /**
     * Retrieve the current size of the processed image and export it to the specified variable name.
     *
     * @param $variable
     *
     * @return $this
     */
    public function exportSize($variable)
    {
        $this->transformations[] = new TB_Image_Transformation_Export_Size($variable);
        return $this;
    }

    /**
     * Retrieve the current width of the processed image and export it to the specified variable name.
     *
     * @param $variable
     *
     * @return $this
     */
    public function exportWidth($variable)
    {
        $this->transformations[] = new TB_Image_Transformation_Export_Width($variable);
        return $this;
    }

    /**
     * Retrieve the current height of the processed image and export it to the specified variable name.
     *
     * @param $variable
     *
     * @return $this
     */
    public function exportHeight($variable)
    {
        $this->transformations[] = new TB_Image_Transformation_Export_Height($variable);
        return $this;
    }

    /**
     * Resize the image to fit inside the specified width & height keeping aspect ratio.
     * If one of them is not specified, the image will be resized only using the other dimension.
     *
     * @param int|null $width
     * @param int|null $height
     *
     * @return $this
     */
    public function resizeContain($width = null, $height = null)
    {
        $this->transformations[] = new TB_Image_Transformation_ResizeContain($width, $height);
        return $this;
    }

    /**
     * Resize the image to fit outside the specified width & height keeping aspect ratio.
     * Any portions of the image outside of the specified bounds will be cropped out.
     *
     * @param int|null $width
     * @param int|null $height
     * @param string $alignHorizontal
     * @param string $alignVertical
     *
     * @return $this
     */
    public function resizeCover(
        $width,
        $height,
        $alignHorizontal = TB_Image_Transformation_ResizeCover::ALIGN_HORIZONTAL_CENTER,
        $alignVertical = TB_Image_Transformation_ResizeCover::ALIGN_VERTICAL_CENTER
    ) {
        $this->transformations[] = new TB_Image_Transformation_ResizeCover($width, $height, $alignHorizontal,
            $alignVertical);
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return TB_Image_Image
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return WP_Image_Editor
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * @param WP_Image_Editor $editor
     *
     * @return $this
     */
    public function setEditor($editor)
    {
        $this->editor = $editor;
        return $this;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasVariable($name)
    {
        return array_key_exists($name, $this->variables);
    }

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setVariable($name, $value)
    {
        $this->variables[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @param null $default
     *
     * @return mixed|null
     */
    public function getVariable($name, $default = null)
    {
        if($this->hasVariable($name)) {
            return $this->variables[$name];
        }
        return $default;
    }
}