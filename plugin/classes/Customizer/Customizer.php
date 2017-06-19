<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Customizer;

use TB\Asset\Backend;
use TB\Form\Form;
use TB\Initializable\InitializableInterface;
use TB\ServiceManager\ServiceManagerAwareInterface;
use TB\ServiceManager\ServiceManagerAwareTrait;
use TB\ServiceManager\ServiceManagerConfigAwareInterface;
use TB\ServiceManager\ServiceManagerConfigAwareTrait;
use TB\Uri\Uri;

/**
 * Customization options manager with WP admin page and WP Customize support.
 *
 * Class Customizer
 * @package TB\Customizer
 */
class Customizer implements
    ServiceManagerAwareInterface,
    ServiceManagerConfigAwareInterface,
    InitializableInterface
{
    use ServiceManagerAwareTrait;
    use ServiceManagerConfigAwareTrait;

    /**
     * Icon to be used for the groups admin menu entry.
     */
    const TB_ICON = <<<TB_ICON
data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIH
htbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMH
B4IiB2aWV3Qm94PSI3NDIgLTMyNCA2OTAgNjkwIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDc0MiAtMzI0IDY5MCA2OTA7IiB4bWw6c3BhY2U9In
ByZXNlcnZlIj48cGF0aCBkPSJNMTM1OC4xLDUzLjJjMCwxMzEuOC0xMDYuOSwyMzguNy0yMzguNywyMzguN2MtMy4zLDAtNi41LTAuMS05LjctMC4ybDUzLj
EtOTEuN2M2My40LTE4LjcsMTA5LjYtNzcuMywxMDkuNi0xNDYuNyBjMC04NC41LTY4LjUtMTUzLTE1My0xNTNoLTkuOGw0Ny44LTgyLjdDMTI3MS4xLTE2NC
4yLDEzNTgtNjUuNiwxMzU4LjEsNTMuMnogTTk2Ni4zLDUzLjN2LTE1M2g4NC41bDQ5LjEtODQuOSBjLTEuNiwwLjEtMy4zLDAuMy00LjksMC40SDk2Ni4zdi
0xMzUuMWwtODUuNyw0OC4ydjg2LjlINzk1bC00OSw4Ni4zaDEzNC4zbDAuMywxNjEuNmgwLjJjNC42LDEwNS4yLDc3LjIsMTkyLjcsMTc0LjksMjE5LjcgbD
Q1LjMtNzguMkMxMDI1LjEsMTk2LjEsOTY2LjMsMTMxLjYsOTY2LjMsNTMuM3oiLz48L3N2Zz4=
TB_ICON;

    /**
     * Customizer groups.
     *
     * @var array
     */
    protected $groups = [];

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        // display registered option groups under one app related menu entry
        add_action('admin_menu', function () {
            $this->getServiceManagerConfig()->depend('app.name');

            /**
             * @var string $name
             * @var array $options
             */
            foreach ($this->groups as $name => $options) {
                if (!isset($root)) {
                    $root = $name;
                    add_menu_page(
                        $this->getServiceManagerConfig()->get('app.name'),
                        $this->getServiceManagerConfig()->get('app.name'),
                        'manage_options',
                        $root,
                        '__return_null',
                        str_replace("\r\n", '', self::TB_ICON),
                        2
                    );
                }
                add_submenu_page(
                    $root,
                    $options['title'],
                    $options['title'],
                    'manage_options',
                    $name,
                    $this->getServiceManager()->create('tb.customizer.handler.admin_page', [
                        $this,
                        $name,
                        $options['title'],
                        $options['form']
                    ])
                );
            }
        });

        // display registered option groups in the WP customize panel
        add_action('customize_register', function () {
            throw new \Exception('TODO customize_register');

//            /**
//             * @var string $page
//             * @var array $options
//             */
//            foreach($this->pages as $page => $options) {
//                /** @var TB_Form_Form $form */
//                $form = is_string($options['form']) ? new $options['form'] : $options['form'];
//
//                if($this->renderWPCustomizeElements($wp_customize, $page, $form->getElementsWithoutPriorities())) {
//                    $wp_customize->add_panel($page, array(
//                        'title' => $options['title'],
//                        'priority' => 2
//                    ));
//                } else {
//                    $wp_customize->add_section($page, array(
//                        'title' => $options['title'],
//                        'priority' => 2
//                    ));
//                }
//            }
        });

        // services
        /** @var Backend $assetBackend */
        $assetBackend = $this->getServiceManager()->get('tb.asset.backend');

        /** @var Uri $uri */
        $uri = $this->getServiceManager()->get('tb.uri.uri');

        // enqueue required assets
        $assetBackend
            ->enqueueMedia()

            ->enqueueStyle('wp-color-picker')
            ->enqueueStyle(
                'tb-backend-customizer-styles',
                $uri->getFrameworkUrl('assets/styles/backend-customizer.css')
            )->enqueueStyle(
                'tb-backend-customizer-elements-styles',
                $uri->getFrameworkUrl('assets/styles/backend-customizer-elements.css'),
                array('tb-backend-customizer-styles')
            )

            ->enqueueScript('wp-color-picker')
            ->enqueueScript(
                'tb-backend-customizer-scripts',
                $uri->getFrameworkUrl('assets/scripts/backend-customizer.min.js')
            )->enqueueScript(
                'tb-backend-customizer-elements-scripts',
                $uri->getFrameworkUrl('assets/scripts/backend-customizer-elements.min.js'),
                array('tb-backend-customizer-scripts')
            );
    }

    /**
     * Register a new option group.
     *
     * The name can be used when fetching stored options, the title will be used for display in the admin panel and
     * WP Customize and the form must either be a \TB\Form\Form instance or a resolvable resource.
     *
     * Set $registerAsAdminPage to TRUE to make the group appear as an admin page.
     *
     * Set $registerWithWPCustomize to TRUE to make the group also appear in the WP Customize panel. For this to work,
     * each form element must also have an option "show_in_wp_customize" set to TRUE.
     *
     * @param string $name
     * @param string $title
     * @param string|Form $form
     * @param bool $registerAsAdminPage
     * @param bool $registerWithWPCustomize
     * @return $this
     * @throws \Exception
     */
    public function register(
        $name,
        $title,
        $form,
        $registerAsAdminPage = false,
        $registerWithWPCustomize = false
    ) {
        if (is_string($form)) {
            /** @var Form $form */
            $form = $this->getServiceManager()->get($form);
        }

        if (!$form instanceof Form) {
            throw new \Exception('Could not register group with customizer, the supplied form cannot be resolved to' .
                ' a valid \TB\Form\Form instance.');
        }

        if ($registerWithWPCustomize && false !== strpos($name, '__')) {
            throw new \Exception('Could not register group with customizer, group names set to register with' .
                ' WP Customize must not include underscores in their names.');
        }

        $this->groups[$name] = [
            'title' => $title,
            'form' => $form,
            'register_as_admin_page' => $registerAsAdminPage,
            'register_with_wp_customize' => $registerWithWPCustomize
        ];

        return $this;
    }

    /**
     * Retrieve the entire unfiltered content of a group.
     * Only use this if you know what you're doing.
     *
     * @param string $name
     * @param null $default
     * @return mixed
     */
    public function getGroupOptions($name, $default = null)
    {
        $this->getServiceManagerConfig()->depend('app.slug');
        return get_option(get_class($this) . '.' . $this->getServiceManagerConfig()->get('app.slug') . '.' . $name,
            $default);
    }

    /**
     * Update the entire unfiltered content of a group.
     * Only use this if you know what you're doing.
     *
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    public function setGroupOptions($name, $value)
    {
        $this->getServiceManagerConfig()->depend('app.slug');
        return update_option(get_class($this) . '.' . $this->getServiceManagerConfig()->get('app.slug') . '.' . $name,
            $value);
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     * @return Customizer
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;
        return $this;
    }
}