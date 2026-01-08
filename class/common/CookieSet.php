<?php
/**
 * Cookie utility functions
 */
class CookieSet
{
    public static function set($name, $value, $expire = 86400): void
    {
        setcookie($name, $value, time() + $expire, '/');
    }

    public static function get($name, $default = '')
    {
        return $_COOKIE[$name] ?? $default;
    }

    public static function delete($name): void
    {
        setcookie($name, '', time() - 3600, '/');
    }
}
