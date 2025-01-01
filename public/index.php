<?php
require __DIR__ . '/../vendor/autoload.php';
require '../helpers.php';

use Framework\Router;

$router = new Router();

// $router is used in routes.php, so make sure $router is defined before routes.php is included
$routes = require basePath('routes.php');

// exclude query params from the uri for route matching
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$method = $_SERVER['REQUEST_METHOD'];

$router->route($uri, $method);
