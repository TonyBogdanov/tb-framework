<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

// converts the specified value of file size notation to it's integer representation
if (!function_exists('tb_ini_size_in_bytes')) {
    function tb_ini_size_in_bytes($val)
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        $val = intval($val);
        switch ($last) {
            case 'g':
                $val *= 1073741824;
                break;

            case 'm':
                $val *= 1048576;
                break;

            case 'k':
                $val *= 1024;
                break;
        }
        return $val;
    }
}

// attempts to raise the memory_limit if needed
// returns TRUE on success (or if not needed) and the current value on failure
if (!function_exists('tb_ensure_memory_limit')) {
    function tb_ensure_memory_limit($required, $changeAttempted = false)
    {
        $current = ini_get('memory_limit');
        if (tb_ini_size_in_bytes($current) < tb_ini_size_in_bytes($required)) {
            if ($changeAttempted) {
                return $current;
            } else {
                ini_set('memory_limit', $required);
                return tb_ensure_memory_limit($required, true);
            }
        }
        return true;
    }
}