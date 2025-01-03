<?php
require __DIR__ . '/../vendor/autoload.php';

use Framework\Router;
use Framework\Session;

Session::start();

require '../helpers.php';

$router = new Router();

// $router is used in routes.php, so make sure $router is defined before routes.php is included
$routes = require basePath('routes.php');

// exclude query params from the uri for route matching
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router->route($uri);
