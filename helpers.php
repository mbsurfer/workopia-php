<?php

/**
 * Get the base path
 * 
 * @param string $path
 * @return string
 */
function basePath($path = '')
{
    return __DIR__ . '/' . $path;
}

/**
 * Check if file exists before making it required
 *
 * @param string $filepath
 * @return void
 */
function tryRequire($filepath = '', $data = [])
{
    if (file_exists($filepath)) {
        extract($data);
        require $filepath;
    } else {
        echo "File '{$filepath}' not found!";
    }
}

/**
 * Load a view
 *
 * @param string $name
 * @return void
 */
function loadView($name = '', $data = [])
{
    $filepath = basePath("App/views/{$name}.view.php");
    tryRequire($filepath, $data);
}

/**
 * Load a partial
 *
 * @param string $name
 * @return void
 */
function loadPartial($name = '', $data = [])
{
    $filepath = basePath("App/views/partials/{$name}.php");
    tryRequire($filepath, $data);
}

/**
 * Inspect a value(s)
 *
 * @param mixed $value
 * @return void
 */
function inspect($value)
{
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
}

/**
 * Inspect a value(s) and die
 *
 * @param mixed $value
 * @return void
 */
function inspectAndDie($value)
{
    inspect($value);
    die();
}

/**
 * Format salary
 *
 * @param string $salary
 * @return void
 */
function formatSalary($salary = '')
{
    return "$" . number_format(floatval($salary));
}

function sanitize($value = '')
{
    return filter_var(trim($value), FILTER_SANITIZE_SPECIAL_CHARS);
}

/**
 * Redirect to url and exit
 *
 * @param string $uri
 * @return void
 */
function redirect($uri = '')
{
    header("Location: {$uri}");
    exit();
}
