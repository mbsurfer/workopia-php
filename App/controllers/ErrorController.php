<?php

namespace App\Controllers;

class ErrorController
{

    public static function notFound($message = 'Resource not found')
    {
        http_response_code(404);
        loadView('error', [
            'status' => 404,
            'message' => $message
        ]);
    }

    public static function unauthorised($message = 'Not authorized to view this resource')
    {
        http_response_code(403);
        loadView('error', [
            'status' => 403,
            'message' => $message
        ]);
    }
}