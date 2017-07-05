<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_ShortCode
 */
abstract class TB_ShortCode
{
    /**
     * @var TB_ShortCode_Manager
     */
    protected $manager;

    /**
     * @var array
     */
    protected $args = array();

    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $attrs = array();

    /**
     * @return string
     */
    abstract public function getTag();

    /**
     * @return string
     */
    abstract public function invoke();

    /**
     * TB_ShortCode constructor.
     *
     * @param TB_ShortCode_Manager $manager
     */
    public function __construct(TB_ShortCode_Manager $manager)
    {
        $this->setManager($manager);
    }

    public function preInvoke()
    {}

    public function postInvoke()
    {}

    /**
     * @return TB_ShortCode_Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param TB_ShortCode_Manager $manager
     *
     * @return TB_ShortCode
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * @param string $set
     *
     * @return TB_ShortCode_AttributeSet
     */
    public function attrs($set = 'default')
    {
        if(!array_key_exists($set, $this->attrs)) {
            $this->attrs[$set] = new TB_ShortCode_AttributeSet($this, $set);
        }
        return $this->attrs[$set];
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param array $args
     *
     * @return TB_ShortCode
     */
    public function setArgs($args)
    {
        $this->args = $args;
        return $this;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setArg($name, $value)
    {
        $this->args[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @param null $default
     *
     * @return mixed|null
     */
    public function getArg($name, $default = null)
    {
        return $this->hasArg($name) ? $this->args[$name] : $default;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasArg($name)
    {
        return array_key_exists($name, $this->args);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function isArgTrue($name)
    {
        if(!$this->hasArg($name)) {
            return false;
        }

        $arg = $this->getArg($name);

        if(is_string($arg)) {
            return 'true' == $arg || '1' == $arg;
        } else {
            return (bool) $arg;
        }
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function isArgFalse($name)
    {
        if(!$this->hasArg($name)) {
            return false;
        }
        return !$this->isArgTrue($name);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function isArgEmpty($name)
    {
        if(!$this->hasArg($name)) {
            return true;
        }

        $arg = $this->getArg($name);

        return empty($arg);
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return TB_ShortCode
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasContent()
    {
        return is_string($this->content);
    }
}