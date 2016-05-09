<?php

namespace Helper;

/**
 * Class Formater
 * @author Simon Vieille <simon@deblan.fr>
 */
class Formater
{
    public static function humanSize($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('B', 'kB', 'MB', 'GB', 'TB');

        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }
}
