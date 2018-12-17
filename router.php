<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 12/17/2018
 * Time: 09:01
 */

global $TITLE;
$request = str_replace("\\", "/", rtrim($_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'], '/'));
//$request = str_replace("\\", "/", __DIR__) . str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['REQUEST_URI']);

define('ROOT_URL', rtrim((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
    "://$_SERVER[HTTP_HOST]" . str_replace("\\", "/", dirname($_SERVER['SCRIPT_NAME'])), '/'));


if (preg_match("/\.(?!php)/", $request)) {
    return false;
} else {
    if (file_exists($request . ".php"))
        include_once $request . ".php";

    include_once 'template/template.php';
    return true;
}

function call_page_function($function, $params = array())
{
    if (function_exists($function))
        call_user_func_array($function, $params);
}

