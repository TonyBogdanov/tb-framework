<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_ShortCode
 *
 * WordPress short code service.
 */
class TB_ShortCode extends TB_ServiceManager_Aware
{
    /**
     * Holds registered short codes.
     *
     * @var array
     */
    protected $shortCodes = array();

    /**
     * TB_ShortCode constructor.
     */
    public function __construct()
    {
        add_action('init', array($this, '__actionInit'));
    }

    /**
     * Register a new short code with the manager.
     * Specify the tag that will trigger the short code and a string short code name resolvable by the service manager.
     *
     * @param $tag
     * @param $shortCode
     * @return $this
     */
    public function register($tag, $shortCode)
    {
        $this->shortCodes[$tag] = $shortCode;
        return $this;
    }

    /**
     * @hook init
     */
    public function __actionInit()
    {
        foreach($this->shortCodes as $tag => $shortCode) {
            add_shortcode($tag, array($this, '__renderShortCode'));
        }
    }

    /**
     * Render a short code.
     *
     * @param $attributes
     * @param $content
     * @param $tag
     * @return string
     * @throws Exception
     */
    public function __renderShortCode($attributes, $content, $tag)
    {
        /** @var TB_ShortCode_ShortCode $shortCode */
        $shortCode = $this->sm()->create($this->shortCodes[$tag]);
        if(!$shortCode instanceof TB_ShortCode_ShortCode) {
            throw new Exception('Registered short code for tag "' . $tag . '" must extend from TB_ShortCode_ShortCode');
        }

        if(!empty($attributes)) {
            $shortCode->setAttributes($attributes);
        }
        if(!empty($content)) {
            $shortCode->setContent($content);
        }

        return $shortCode->render();
    }
}