<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Customizer\Handler;

use TB\Customizer\Customizer;
use TB\DOM\DOM;
use TB\Form\Form;
use TB\ServiceManager\ServiceManagerAwareInterface;
use TB\ServiceManager\ServiceManagerAwareTrait;

/**
 * Admin page renderer for customizer groups.
 *
 * Class AdminPageHandler
 * @package TB\Customizer
 */
class AdminPage implements
    ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

    /**
     * A reference to the customizer object that created this handler.
     *
     * @var Customizer
     */
    protected $customizer;

    /**
     * Group name.
     *
     * @var string
     */
    protected $name;

    /**
     * Page title.
     *
     * @var string
     */
    protected $title;

    /**
     * Bound form.
     *
     * @var Form
     */
    protected $form;

    /**
     * AdminPageHandler constructor.
     * @param Customizer $customizer
     * @param $name
     * @param $title
     * @param Form $form
     */
    public function __construct(Customizer $customizer, $name, $title, Form $form)
    {
        $this->setCustomizer($customizer);
        $this->setName($name);
        $this->setTitle($title);
        $this->setForm($form);
    }

    /**
     * Render the admin page.
     */
    public function __invoke()
    {
        // attach the admin page decorator
        /** @var \TB\Form\Decorator\AdminPage $decorator */
        $decorator = $this->getServiceManager()->create('tb.form.decorator.admin_page');
        $this->getForm()->addDecoratorDeep($decorator);

        // populate the form with data from db
        $data = $this->getCustomizer()->getGroupOptions($this->getName(), []);
        if (!is_array($data)) {
            $data = [];
        }
        $this->getForm()->setSerializedData($data);

        // form submitted
        if (isset($_SERVER['REQUEST_METHOD']) && 'POST' == $_SERVER['REQUEST_METHOD']) {
            $this->getForm()->setSerializedData(stripslashes_deep($_POST));
            if ($this->getForm()->isValid()) {
                $this->getCustomizer()->setGroupOptions($this->getName(), $this->getForm()->getSerializedData());
                $this->getCustomizer()->mirrorOptionsToThemeMods($this->getName());
            }
        }

        echo '<div class="wrap"><h1>' . esc_html($this->getTitle()) . '</h1><p>&nbsp;</p>';
        echo $this->getForm()->render()->append(DOM::create('<p/>')->attrs([
            'class' => 'submit'
        ])->append(DOM::create('<input/>')->attrs([
            'type' => 'submit',
            'class' => 'button-primary',
            'value' => esc_attr__('Save Changes', 'tb')
        ])));
        echo '</div>';
    }

    /**
     * @return Customizer
     */
    public function getCustomizer()
    {
        return $this->customizer;
    }

    /**
     * @param Customizer $customizer
     * @return $this
     */
    public function setCustomizer(Customizer $customizer)
    {
        $this->customizer = $customizer;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
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
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param Form $form
     * @return $this
     */
    public function setForm(Form $form)
    {
        $this->form = $form;
        return $this;
    }
}