<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Customizer_Manager
 *
 * Options manager with support for WordPress Customizer and admin pages.
 */
class TB_Customizer extends TB_Config_Aware implements TB_Initializable
{
    /**
     * Customizer groups.
     *
     * @var array
     */
    protected $groups = array();

    /**
     * Temporarily block theme mod update hooks to avoid recursion.
     *
     * @var bool
     */
    protected $blockUpdateHooks = false;
//
//    /**
//     * @param $old
//     * @param array $data
//     *
//     * @return $this
//     */
//    protected function updateOptionsFromThemeMods($old, array $data)
//    {
//        if($this->blockUpdateHooks) {
//            return $this;
//        }
//
//        $lookupRegex = '/^(?P<page>';
//        foreach(array_keys($this->pages) as $page) {
//            $lookupRegex .= preg_quote($page, '/') . '|';
//        }
//        $lookupRegex = substr($lookupRegex, 0, -1) . ')__(?P<trail>.+)$/';
//
//        foreach($data as $key => $value) {
//            if(preg_match($lookupRegex, $key, $matches)) {
//                $this->setOption($matches['page'], explode('__', $matches['trail']), $value, true);
//            }
//        }
//
//        return $this;
//    }

    /**
     * Updates theme mods from the corresponding options from a form element.
     *
     * @param TB_Form_Element $element
     * @param string $prefix
     * @param bool $root
     *
     * @return mixed
     */
    protected function updateThemeModsFromOptions(TB_Form_Element $element, $prefix, $root = true)
    {
        if($this->blockUpdateHooks) {
            return $this;
        }

        $data = array();

        if($element instanceof TB_Form_Element_Fieldset) {
            /** @var TB_Form_Element $childElement */
            foreach($element->getElementsWithoutPriorities() as $childElement) {
                $data = array_merge($data, $this->updateThemeModsFromOptions($childElement,
                    $prefix . $element->getName() . '__', false));
            }
        } else {
            $data[$prefix . $element->getName()] = $element->getSerializedData();
        }

        if(!$root) {
            return $data;
        }

        $this->blockUpdateHooks = true;
        foreach($data as $key => $value) {
            set_theme_mod($key, $value);
        }
        $this->blockUpdateHooks = false;

        return $this;
    }

//    public function mirrorThemeModsToOptions()
//    {}

    /**
     * Mirrors the options stored for a group and updates the corresponding theme mods.
     *
     * @param string $name
     * @return $this
     * @throws Exception
     */
    public function mirrorOptionsToThemeMods($name)
    {
        if(!array_key_exists($name, $this->groups)) {
            throw new Exception('Group is not registered with the customizer: ' . $name);
        }

        /** @var TB_Form_Form $form */
        $form = $this->groups[$name]['form'];

        $form->setSerializedData($this->getGroupOptions($name));
        if(!$form->isValid()) {
            throw new Exception('Could not mirror group: ' . $name . ', data stored in the database does not' .
                                ' comply with the corresponding form structure.');
        }

        return $this->updateThemeModsFromOptions($form, $name);
    }

    /**
     * TB_Customizer constructor.
     */
    public function __construct()
    {
        // add hooks
        add_action('customize_register', array($this, '__actionCustomizeRegister'));
        add_action('admin_menu', array($this, '__actionAdminMenu'));

    }

//    /**
//     * @param $page
//     *
//     * @return TB_Form_Form
//     */
//    protected function getCachedForm($page)
//    {
//        if(!isset($this->formCache[$page])) {
//            $formClass = $this->pages[$page]['form'];
//            $this->formCache[$page] = is_string($formClass) ? new $formClass : $formClass;
//        }
//        return $this->formCache[$page];
//    }

//    /**
//     * @inheritDoc
//     */
//    public function __call($name, $arguments)
//    {
//        if('renderAdminPage__' == substr($name, 0, 17)) {
//            $name = substr($name, 17);
//            if(array_key_exists($name, $this->pages)) {
//                return $this->renderAdminPage($name);
//            }
//        }
//        trigger_error('Call to undefined method ' . get_class($this) . '::' . $name . '()', E_USER_ERROR);
//    }

    /**
     * Retrieve the stored option value for the specified group and trail.
     *
     * The trail can either be an array of strings (target option names), or a string of target option names divided
     * by any character that does not comply to the form element name regex range, e.g. slash, dot or whitespace.
     *
     * If the value is not found and a "default" option is set in the corresponding form element, that value is returned.
     * Otherwise, the specified $default value is used.
     *
     * @param string $name
     * @param string|array $trail
     * @param null $default
     *
     * @return mixed|null
     * @throws Exception
     */
    public function getOption($name, $trail, $default = null)
    {
        // test and normalize trail
        if(!array_key_exists($name, $this->groups)) {
            throw new Exception('Group is not registered with the customizer: ' . $name);
        }

        if(is_string($trail)) {
            $trail = preg_split('/[^' . TB_Form_Element::NAME_REGEX_RANGE . ']+/', $trail);
        }
        if(!is_array($trail)) {
            throw new Exception('Second argument (trail) must either be a string, or an array of strings, got: ' .
                                gettype($trail));
        }

        /** @var string $path */
        foreach($trail as $index => $path) {
            if(!is_string($path)) {
                throw new Exception('When the second argument (trail) is an array, all elements must be valid ' .
                                    'strings, got: ' . gettype($path) . ' at index ' . $index);
            }
        }

        // traverse options
        $traverseOption = $this->getGroupOptions($name, array());
        if(!is_array($traverseOption)) {
            $traverseOption = array();
        }

        $traverseTrail = $trail;
        $optionExists = true;

        do {
            $key = array_shift($traverseTrail);
            if(array_key_exists($key, $traverseOption)) {
                $traverseOption = $traverseOption[$key];
            } else {
                $optionExists = false;
                break;
            }
        } while(!empty($traverseTrail));

        // traverse form element
        /** @var TB_Form_Form $traverseElement */
        $traverseElement = $this->groups[$name]['form'];
        $traverseTrail = $trail;
        $elementExists = true;

        do {
            $key = array_shift($traverseTrail);
            if($traverseElement instanceof TB_Form_Element_Fieldset && $traverseElement->hasElement($key)) {
                $traverseElement = $traverseElement->getElement($key);
            } else {
                $elementExists = false;
                break;
            }
        } while(!empty($traverseTrail));

        // bail early if corresponding element does not exist
        if(!$elementExists) {
            return $optionExists ? $traverseOption : $default;
        }

        // apply element filters on the value
        if($optionExists) {
            return $traverseElement->setSerializedData($traverseOption)->getData();
        }

        // fallback to element's default value
        try {
            return $traverseElement->getOption('default');
        } catch(Exception $e) {
            return $default;
        }
    }

    /**
     * Update the value of the specified group name and form element trail.
     * If $mute is set to TRUE, the function will not throw an exception when the trail does not exist in the group.
     *
     * @param string $name
     * @param string|array $trail
     * @param mixed $value
     * @param bool $mute
     * @return $this
     * @throws Exception
     */
    public function setOption($name, $trail, $value, $mute = false)
    {
        if(!array_key_exists($name, $this->groups)) {
            throw new Exception('Group is not registered with the customizer: ' . $name);
        }

        if(is_string($trail)) {
            $trail = array($trail);
        }
        if(!is_array($trail)) {
            throw new Exception('Second argument (trail) must either be a string, or an array of strings, got: ' .
                                gettype($trail));
        }

        /** @var TB_Form_Form $form */
        $form = $this->groups[$name]['form'];

        $traverse = $form;
        $traverseTrail = $trail;
        $targetExists = true;

        do {
            $key = array_shift($traverseTrail);
            if($traverse instanceof TB_Form_Element_Fieldset && $traverse->hasElement($key)) {
                $traverse = $traverse->getElement($key);
            } else {
                $targetExists = false;
                break;
            }
        } while(!empty($traverseTrail));

        if(!$targetExists) {
            if($mute) {
                return $this;
            }
            throw new Exception('Cannot set option for group: ' . $name . ', option does not exist in the structure: ' .
                                implode(' -> ', $trail));
        }

        $options = $this->getGroupOptions($name, array());
        if(!is_array($options)) {
            $options = array();
        }

        $traverse = &$options;
        $traverseTrail = $trail;

        do {
            $key = array_shift($traverseTrail);
            if(!array_key_exists($key, $traverse)) {
                $traverse[$key] = array();
            }
            $traverse = &$traverse[$key];
        } while(!empty($traverseTrail));

        $traverse = $value;
        unset($traverse);

        $this->setGroupOptions($name, $options);

        return $this;
    }

//
//    /**
//     * Has the manager been initialized.
//     *
//     * @return bool
//     */
//    public function isInited()
//    {
//        return $this->inited;
//    }

//    /**
//     * @return $this
//     */
//    public function hookCustomizeControlsEnqueueScripts()
//    {
//        wp_enqueue_script('tb-backend-customizer-elements',
//            $this->getApp()->uri()->framework('assets/scripts/backend-customizer-elements.min.js'),
//            array('jquery', 'customize-controls'), false, true);
//
//        return $this;
//    }

//    /**
//     * @param WP_Customize_Manager $wp_customize
//     * @param $id
//     * @param array $elements
//     *
//     * @return bool
//     * @throws Exception
//     */
//    public function renderWPCustomizeElements(WP_Customize_Manager $wp_customize, $id, array $elements)
//    {
//        $hasFieldsets = false;
//
//        /** @var TB_Form_Element $element */
//        foreach($elements as $element) {
//            if(false !== strpos($element->getName(), '__')) {
//                throw new Exception('Could not register with customizer, form elements\' names cannot include' .
//                                    ' double underscores: ' . $element->getName());
//            }
//
//            if($element instanceof TB_Form_Element_Fieldset) {
//                $hasFieldsets = true;
//
//                $wp_customize->add_section($id . '__' . $element->getName(), array(
//                    'title' => $element->getOption('title'),
//                    'panel' => $id,
//                    'priority' => 2
//                ));
//
//                if($this->renderWPCustomizeElements($wp_customize, $id . '__' . $element->getName(),
//                    $element->getElementsWithoutPriorities())) {
//                    throw new Exception('Form is not compatible with WordPress Customize because it has more than' .
//                                        ' one level of fieldsets.');
//                }
//            } else if($element->hasOption('show_in_wp_customize') && $element->getOption('show_in_wp_customize')) {
//                $settingOptions = array(
//                    'transport' => 'refresh'
//                );
//                if($element->hasOption('default') || $element->hasDefaultOption('default')) {
//                    $settingOptions['default'] = $element->getOption('default');
//                }
//
//                $wp_customize->add_setting($id . '__' . $element->getName(), $settingOptions);
//                $wp_customize->add_control(new TB_Form_Adapter_Customize(
//                    $element,
//                    $wp_customize,
//                    $id . '__' . $element->getName(),
//                    array(
//                        'label' => $element->getOption('title'),
//                        'description' => $element->hasOption('description') ? $element->getOption('description') : '',
//                        'section' => $id,
//                        'settings' => $id . '__' . $element->getName()
//                    )
//                ));
//            }
//        }
//
//        return $hasFieldsets;
//    }
}