<?php

namespace Framework;

class Validation
{

    /**
     * Validate a string value based on a minimum and maximum length
     *
     * @param string $value
     * @param int $min
     * @param int $max
     * @return bool
     */
    public static function string($value = '', $min = 1, $max = INF)
    {
        if (!is_string($value)) {
            return false;
        }

        $length = strlen(trim($value));

        return $length >= $min && $length <= $max;
    }

    /**
     * Validate an email address string value based on the PHP filter_var function. 
     * Return the filtered value if it is a valid email address, otherwise return false.
     *
     * @param string $value
     * @return mixed
     */
    public static function email($value = '')
    {
        return filter_var(trim($value), FILTER_VALIDATE_EMAIL);
    }

    /**
     * Match a string value with another string value
     *
     * @param string $value
     * @param string $match
     * @return void
     */
    public static function match($value = '', $match = '')
    {
        return trim($value) === trim($match);
    }
}
