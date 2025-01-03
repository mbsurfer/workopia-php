<?php

namespace Framework;

class Session
{
    public static function start()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key = '', $value = '')
    {
        $_SESSION[$key] = $value;
    }

    public static function has($key = '')
    {
        return isset($_SESSION[$key]);
    }

    public static function get($key = '')
    {
        return $_SESSION[$key] ?? null;
    }

    public static function clear($key = '')
    {
        unset($_SESSION[$key]);
    }

    public static function clearAll()
    {
        session_unset();
        session_destroy();
    }
}
