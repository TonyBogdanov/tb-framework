<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_MetaBox_MetaBox
 *
 * Meta box representation class.
 */
class TB_MetaBox_MetaBox extends TB_ServiceManager_Aware
{
    const CONTEXT_NORMAL    = 'normal';
    const CONTEXT_SIDE      = 'side';
    const CONTEXT_ADVANCED  = 'advanced';

    const PRIORITY_HIGH     = 'high';
    const PRIORITY_LOW      = 'low';
    const PRIORITY_DEFAULT  = 'default';

    /**
     * @var string
     */
    protected $title;

    /**
     * @var mixed
     */
    protected $screen;

    /**
     * @var string
     */
    protected $context = self::CONTEXT_ADVANCED;

    /**
     * @var string
     */
    protected $priority = self::PRIORITY_DEFAULT;

    /**
     * @var callable
     */
    protected $renderCallback;

    /**
     * @var callable
     */
    protected $saveCallback;

    /**
     * @param $screen
     *
     * @return null|WP_Screen
     */
    protected function normalizeScreen($screen)
    {
        if(empty($screen)) {
            if(!function_exists('get_current_screen')) {
                require_once ABSPATH . 'wp-admin/includes/screen.php';
            }
            $screen = get_current_screen();
        } elseif (is_string($screen)) {
            if(!function_exists('get_current_screen')) {
                require_once ABSPATH . 'wp-admin/includes/screen.php';
            }
            if(!class_exists('WP_Screen')) {
                require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
            }
            $screen = WP_Screen::get($screen);
        } else if(is_array($screen)) {
            foreach($screen as &$single) {
                $single = $this->normalizeScreen($single);
            }
            unset($single);
        }
        return $screen;
    }

    /**
     * TB_MetaBox_MetaBox constructor.
     *
     * @param string $title
     */
    public function __construct($title)
    {
        $this->setTitle($title);
    }

    /**
     * @param WP_Post $post
     * @throws Exception
     */
    public function render(WP_Post $post)
    {
        if(!isset($this->renderCallback)) {
            throw new Exception('Could not render meta box ' . $this->getId() .
                                '. Either override the render() method or set a render callback.');
        }
        call_user_func($this->renderCallback, $post);
    }

    /**
     * @param WP_Post $post
     * @throws Exception
     */
    public function save(WP_Post $post)
    {
        if(!isset($this->saveCallback)) {
            throw new Exception('Could not save meta box ' . $this->getId() .
                                '. Either override the save() method or set a save callback. You can also set it' .
                                ' to __return_null if you don\'t need to save the meta box.');
        }
        call_user_func($this->saveCallback, $post);
    }

    /**
     * Returns TRUE if the current widget is active on the specified screen.
     * If the specified screen is an array, it will return TRUE if the widget matches at least one of the screens.
     *
     * @param WP_Screen|array|null $screen
     * @param WP_Screen|array|null $widgetScreen
     * @return bool
     */
    public function isActiveOnScreen($screen, $widgetScreen = null)
    {
        // if widget screen isn't set, then it's active on *any* screen
        if(!isset($widgetScreen)) {
            $widgetScreen = $this->getScreen();
        }
        if(!isset($widgetScreen)) {
            return true;
        }

        // if widget is assigned multiple screens, return TRUE if at least one matches
        if(is_array($widgetScreen)) {
            foreach($widgetScreen as $widgetSubScreen) {
                if($this->isActiveOnScreen($screen, $widgetSubScreen)) {
                    return true;
                }
            }
            return false;
        }

        // widget screen is normalized, compare screen might not be
        $screen = $this->normalizeScreen($screen);

        // at this point, widget screen should be a single WP_Screen object
        // if compare screen is null, it means no match
        if(!isset($screen)) {
            return false;
        }

        // if compare screen is an array, return TRUE if at least one matches
        if(is_array($screen)) {
            foreach($screen as $subScreen) {
                if($this->isActiveOnScreen($subScreen, $widgetScreen)) {
                    return true;
                }
            }
            return false;
        }

        // both widget and compare screens are WP_Screen objects, compare by id
        return $screen->id === $widgetScreen->id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return md5(serialize(array(
            $this->getTitle(),
            $this->getScreen(),
            $this->getContext(),
            $this->getPriority()
        )));
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return TB_MetaBox_MetaBox
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getScreen()
    {
        return $this->screen;
    }

    /**
     * @param mixed $screen
     * @return TB_MetaBox_MetaBox
     */
    public function setScreen($screen)
    {
        $this->screen = $this->normalizeScreen($screen);
        return $this;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param string $context
     * @return TB_MetaBox_MetaBox
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param string $priority
     * @return TB_MetaBox_MetaBox
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return callable
     */
    public function getRenderCallback()
    {
        return $this->renderCallback;
    }

    /**
     * @param callable $renderCallback
     * @return TB_MetaBox_MetaBox
     */
    public function setRenderCallback($renderCallback)
    {
        $this->renderCallback = $renderCallback;
        return $this;
    }

    /**
     * @return callable
     */
    public function getSaveCallback()
    {
        return $this->saveCallback;
    }

    /**
     * @param callable $saveCallback
     * @return TB_MetaBox_MetaBox
     */
    public function setSaveCallback($saveCallback)
    {
        $this->saveCallback = $saveCallback;
        return $this;
    }
}