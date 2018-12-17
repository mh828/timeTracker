<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 12/17/2018
 * Time: 09:01
 */

global $TITLE;
//$request = str_replace("\\", "/", rtrim($_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'], '/'));
//$request = str_replace("\\", "/", __DIR__) . str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['REQUEST_URI']);


$route_relative_url = dirname(str_replace(str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']), '', str_replace("\\", "/", __FILE__)));

define('ROOT_URL', rtrim((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
    "://$_SERVER[HTTP_HOST]" . str_replace("\\", "/", $route_relative_url), '/'));
define('REQUEST', rtrim(str_replace($route_relative_url, '', $_SERVER['REQUEST_URI']), '/'));
define('ROOT_PATH', str_replace("\\", "/", __DIR__));


//auto class loader
spl_autoload_register(function ($cls_path) {
    $path = ROOT_PATH . '/includes/classes/' . str_replace("\\", "/", $cls_path) . ".php";
    if (file_exists($path))
        include_once $path . '';
});

if (preg_match("/\.(?!php)/", $_SERVER['REQUEST_URI'])) {
    return false;
} else {
    $request = ROOT_PATH . '/pages' . REQUEST;
    if (is_dir($request) && file_exists($request . '/index.php'))
        include_once $request . '/index.php';
    else if (file_exists($request . '.php'))
        include_once $request . '.php';

    include_once 'template/template.php';
    return true;
}

function call_page_function($function, $params = array())
{
    if (function_exists($function))
        call_user_func_array($function, $params);
}

