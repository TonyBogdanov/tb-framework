<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Navigation;

use TB\Initializable\InitializableInterface;
use TB\ServiceManager\ServiceManagerAwareInterface;
use TB\ServiceManager\ServiceManagerAwareTrait;

/**
 * WordPress navigation manager.
 *
 * Class Navigation
 * @package TB\Navigation
 */
class Navigation implements
    ServiceManagerAwareInterface,
    InitializableInterface
{
    use ServiceManagerAwareTrait;

    /**
     * Navigation menu locations.
     *
     * @var array
     */
    protected $locations = [];

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        add_action('after_setup_theme', function () {
            foreach ($this->locations as $location => $name) {
                register_nav_menu($location, $name);
            }
        });
    }

    /**
     * Register a new menu location.
     *
     * @param string $location
     * @param string $name
     * @return $this
     */
    public function registerLocation($location, $name)
    {
        $this->locations[$location] = $name;
        return $this;
    }
}