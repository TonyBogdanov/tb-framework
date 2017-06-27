<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

// PHP 5.4+ is assumed from this point on
// prevent direct access
defined('ABSPATH') || die;

// framework version
defined('TB_FRAMEWORK') || define('TB_FRAMEWORK', '1.0.0');

// init the auto loader
require_once __DIR__ . '/vendor/autoload.php';

// enable pretty debugs
\Symfony\Component\Debug\Debug::enable();


// todo experiments


$form = new \TB\Form\Form();
$form->addDecoratorDeep(new \TB\Form\Decorator\AdminPage());

$form->addElement(new \TB\Form\Element\Text('text'));

dump((string) $form->render());
exit;