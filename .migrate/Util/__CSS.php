<?php
/**
 * @package    Tundra
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    ThemeForest Standard License https://themeforest.net/licenses/standard
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Util_CSS
 */
class TB_Util_CSS
{
    /**
     * @param array $vars
     * @param $css
     *
     * @return mixed
     */
    public static function replaceVars(array $vars, $css)
    {
        foreach($vars as $key => $value) {
            $css = preg_replace('/var\h*\(\h*' . preg_quote($key, '/') . '\h*(?:,[^\)]+)?\h*\)/i', $value, $css);
        }
        return $css;
    }
}