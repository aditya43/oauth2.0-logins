<?php

namespace Adi\Classes\Core;

class Session
{
    private function _init()
    {
        if (session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }
    }

    public static function get($key)
    {
        self::_init();
        return (self::exists($key) && $_SESSION[$key]) ? $_SESSION[$key] : false;
    }

    public static function put($key, $value)
    {
        self::_init();
        return $_SESSION[$key] = $value;
    }

    public static function exists($key)
    {
        self::_init();
        return (isset($_SESSION[$key])) ? true : false;
    }

    public static function delete($key)
    {
        self::_init();
        unset($_SESSION[$key]);
    }
}
