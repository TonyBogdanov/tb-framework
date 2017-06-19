<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Element_SingleMedia
 */
class TB_Form_Element_SingleMedia extends TB_Form_Element_MultiMedia
{
    /**
     * @inheritDoc
     */
    public function __construct($name, array $options = array())
    {
        parent::__construct($name, array_replace(array(
            'limit' => 1
        ), $options));
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $data = parent::getData();
        return empty($data) ? false : current($data);
    }

    /**
     * @inheritDoc
     */
    public function setData($value)
    {
        return parent::setData(array($value));
    }

    /**
     * @inheritDoc
     */
    public function render(array $parents = array())
    {
        return $this->decorate(__CLASS__, parent::render($parents), $parents);
    }
}