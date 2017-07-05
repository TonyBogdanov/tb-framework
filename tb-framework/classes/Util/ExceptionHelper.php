<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Framework\Util;

class ExceptionHelper
{
    /**
     * Format the specified value for display in exception messages.
     *
     * @param string $value
     * @return string
     */
    public static function formatParameter($value)
    {
        switch (true) {
            case is_string($value):
                if (class_exists($value)) {
                    return 'class: ' . $value;
                }

                if (interface_exists($value)) {
                    return 'interface: ' . $value;
                }

                if (false !== strpos($value, '::')) {
                    list($class, $propertyOrMethod) = explode('::', $value, 2);
                    if (method_exists($class, $propertyOrMethod)) {
                        return $value . '()';
                    } else if (property_exists($class, $propertyOrMethod)) {
                        return $value;
                    }
                }

                return '"' . $value . '"';

            case is_scalar($value):
                return (string) $value;

            default:
                return '(' . gettype($value) . ')';
        }
    }

    /**
     * Format the specified message and replace all tokens present in the specified parameters with their
     * formatted values.
     *
     * @param $message
     * @param array $parameters
     * @return string
     */
    public static function format($message, array $parameters = [])
    {
        foreach ($parameters as $key => $value) {
            $message = str_replace(':' . $key, self::formatParameter($value), $message);
        }
        return ucfirst($message);
    }
}