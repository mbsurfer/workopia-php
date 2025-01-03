<?php

namespace Framework;

use Framework\Database;

class Controller
{

    protected $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }
}
