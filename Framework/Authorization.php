<?php

namespace Framework;

use Framework\Session;

class Authorization
{
    public static function isOwner($resourceUserId)
    {
        if (!Session::has('user')) {
            return false;
        }
        return ((int) Session::get('user')['id'] ?? 0) === $resourceUserId;
    }
}
