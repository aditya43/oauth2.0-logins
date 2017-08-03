<?php

namespace Adi\Classes\Core;

class App
{
    private static $_registery = [];

    public static function bind($key, $value)
    {
        static::$_registery[$key] = $value;
    }

    public static function get($key)
    {
        if (!array_key_exists($key, static::$_registery))
        {
            throw new Exception("No {$key} is bound in the container", 1);
        }
        return static::$_registery[$key];
    }
}
