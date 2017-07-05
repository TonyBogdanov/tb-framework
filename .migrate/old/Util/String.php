<?php
/**
 * @package    DA Software Co.
 * @author     Tony Bogdanov <support@tonybogdanov.com>
 * @license    Proprietary Software
 * @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

/**
 * Class TB_Util
 *
 * Common shared string utilities.
 */
class TB_Util_String extends TB_ServiceManager_Aware
{
    /**
     * Returns TRUE if the specified value can be type casted to string.
     *
     * @param mixed $value
     * @return bool
     */
    public function canBeCastToString($value)
    {
        return null === $value || is_scalar($value) || (is_object($value) && method_exists($value, '__toString'));
    }
}