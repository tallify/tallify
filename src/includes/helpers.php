<?php

namespace Tallify;

use Symfony\Component\VarDumper\VarDumper;

if (!function_exists('dd')) {
    /**
     * @return never
     */
    function dd(...$vars)
    {
        if (!in_array(\PHP_SAPI, ['cli', 'phpdbg'], true) && !headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
        }

        foreach ($vars as $v) {
            VarDumper::dump($v);
        }

        exit(1);
    }
}

if (!function_exists('tap')) {
    /**
     * Tap the given value.
     *
     * @param  mixed  $value
     * @param  callable  $callback
     * @return mixed
     */
    function tap($value, callable $callback)
    {
        $callback($value);

        return $value;
    }
}

if (!function_exists('flatten')) {
    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  iterable  $array
     * @param  int  $depth
     * @return array
     */
    function flatten($array, $depth = INF)
    {
        $result = [];

        foreach ($array as $item) {
            if (!is_array($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1
                    ? array_values($item)
                    : flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }
}

/**
 * Get the user.
 */
function user()
{
    if (!isset($_SERVER['SUDO_USER'])) {
        return $_SERVER['USER'];
    }

    return $_SERVER['SUDO_USER'];
}

/**
 * Remove any trailing slash if any.
 */
function removeTrailingSlash($string)
{
    if (substr($string, -1) == '/') {
        $string = substr($string, 0, -1);
    }

    return $string;
}
