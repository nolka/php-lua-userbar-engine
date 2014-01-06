<?php
/**
 * User: nolka
 * Date: 06.01.14
 * Time: 23:00
 */

function getattr($var, $param, $default = null)
{
    if (is_array($var))
    {
        if (isset($var[$param]))
            return $var[$param];
    } else if (is_object($var))
    {
        if (property_exists($var, $param))
            return $var->$param;
    }
    return $default;
}