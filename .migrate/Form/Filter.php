<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Form_Filter
 */
abstract class TB_Form_Filter extends TB_Config_Configurable implements TB_Initializable
{
    /**
     * This is used to hold the configuration options passed to the constructor until it's time to pass them to the
     * own config. This is required since the service manager instance is not available in the constructor.
     *
     * @var array
     */
    protected $__delayedConfig = array();

    /**
     * Filters with a smaller priority value are executed earlier.
     * Filters with the same priority value are executed in the order they are registered.
     *
     * @var int
     */
    protected $priority = 10;

    /**
     * @param $value
     * @param TB_Form_Element $element
     *
     * @return mixed
     */
    abstract public function filter($value, TB_Form_Element $element);

    /**
     * TB_Form_Filter constructor.
     *
     * @param array $config
     * @param int $priority
     */
    public function __construct(array $config = array(), $priority = 0)
    {
        $this->__delayedConfig = $config;
        $this->setPriority($priority);
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->ownConfig()->set($this->__delayedConfig);
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     *
     * @return TB_Form_Filter
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }
}