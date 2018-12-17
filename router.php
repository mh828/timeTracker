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
define('REQUEST', str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['REQUEST_URI']));
define('ROOT_PATH', str_replace("\\", "/", __DIR__));


if (preg_match("/\.(?!php)/", $_SERVER['REQUEST_URI'])) {
    return false;
} else {
    $page = ROOT_PATH . '/pages' . REQUEST . '.php';
    if (file_exists($page))
        include_once $page . '';

    include_once 'template/template.php';
    return true;
}

function call_page_function($function, $params = array())
{
    if (function_exists($function))
        call_user_func_array($function, $params);
}

