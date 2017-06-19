<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Util_Dev
 */
class TB_Util_Dev
{
    /**
     * @param $iterations
     * @param $callable
     */
    public function testSpeed($iterations, $callable)
    {
        $start = microtime(true);
        $arguments = func_get_args();

        array_shift($arguments);
        array_shift($arguments);

        for($i = 0; $i < $iterations; $i++) {
            call_user_func_array($callable, $arguments);
        }

        var_dump(number_format(microtime(true) - $start, 10));
        exit(__METHOD__);
    }
}