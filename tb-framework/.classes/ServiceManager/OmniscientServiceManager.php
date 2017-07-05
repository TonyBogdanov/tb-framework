<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\ServiceManager;

use TB\Asset\AssetAwareInterface;
use TB\Asset\AssetAwareTrait;
use TB\Asset\BackendAwareInterface;
use TB\Asset\BackendAwareTrait;
use TB\Asset\EditorAwareInterface;
use TB\Asset\EditorAwareTrait;
use TB\Asset\FrontendAwareInterface;
use TB\Asset\FrontendAwareTrait;
use TB\Config\ConfigAwareInterface;
use TB\Config\ConfigAwareTrait;
use TB\Customizer\CustomizerAwareInterface;
use TB\Customizer\CustomizerAwareTrait;
use TB\Navigation\NavigationAwareInterface;
use TB\Navigation\NavigationAwareTrait;
use TB\Uri\UriAwareInterface;
use TB\Uri\UriAwareTrait;
use TB\Utils\DevelopmentAwareInterface;
use TB\Utils\DevelopmentAwareTrait;
use TB\Utils\PluginAwareInterface;
use TB\Utils\PluginAwareTrait;
use TB\Utils\ThemeAwareInterface;
use TB\Utils\ThemeAwareTrait;
use TB\Utils\WordPressAwareInterface;
use TB\Utils\WordPressAwareTrait;

/**
 * An omniscient version of the service manager "aware" of all possible services available in the framework.
 * This can be used instead of the generic service manager to shorten calls to standard, often-used services, and aid
 * IDE code completion.
 *
 * For example, in order to use a service with code completion you would need to write something like this:
 *
 * @var \The\Name\Of\The\Service $service
 * $service = $serviceManager->get('the.name.of.the.service');
 * $service->doStuff();
 *
 * As you can see, you need to declare the type of the $service variable in order for code completion to work on it.
 * With the omniscient service manager, you can do it like this:
 *
 * $serviceManager->getTheNameOfTheService()->duStuff();
 *
 * Given, that the service is part of the framework package of course.
 *
 * Class OmniscientServiceManager
 * @package TB\ServiceManager
 */
class OmniscientServiceManager extends ServiceManager implements
    ConfigAwareInterface,
    DevelopmentAwareInterface,
    WordPressAwareInterface,
    ThemeAwareInterface,
    PluginAwareInterface,
    NavigationAwareInterface,
    UriAwareInterface,
    AssetAwareInterface,
    FrontendAwareInterface,
    BackendAwareInterface,
    EditorAwareInterface,
    CustomizerAwareInterface
{
    use ConfigAwareTrait;
    use DevelopmentAwareTrait;
    use WordPressAwareTrait;
    use ThemeAwareTrait;
    use PluginAwareTrait;
    use NavigationAwareTrait;
    use UriAwareTrait;
    use AssetAwareTrait;
    use FrontendAwareTrait;
    use BackendAwareTrait;
    use EditorAwareTrait;
    use CustomizerAwareTrait;
}