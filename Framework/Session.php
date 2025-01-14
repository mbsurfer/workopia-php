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

    public static function get($key = '', $default = null)
    {
        return $_SESSION[$key] ?? $default;
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

    public static function setFlashMessage($key = '', $message = '')
    {
        self::set('flash_' . $key, $message);
    }

    public static function getFlashMessage($key = '', $default = null)
    {
        $message = self::get('flash_' . $key, $default);
        self::clear('flash_' . $key);
        return $message;
    }

    public static function checkFlashMessage($key = '')
    {
        if (self::get('flash_' . $key)) {
            return true;
        }
        return false;
    }
}
