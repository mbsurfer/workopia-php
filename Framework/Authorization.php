<?php

namespace Framework;

use Framework\Session;

class Authorization
{
    public static function isOwner($resourceUserId)
    {
        return ((int) Session::get('user')['id'] ?? 0) === $resourceUserId;
    }
}
