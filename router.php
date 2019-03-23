<?php
/**
 * Router that handle all request
 * http://php.net/manual/en/features.commandline.webserver.php
 *
 * Created by PhpStorm.
 * User: Mahdi Hasanpour
 * Date: 11/8/2018
 * Time: 17:58
 */

include_once 'includes/general.php';
ini_set("zlib.output_compression", "on");

//auto class loader
spl_autoload_register(function ($cls_path) {
    $path = __DIR__ . '/includes/classes/' . str_replace("\\", "/", $cls_path) . ".php";
    if (file_exists($path))
        include_once $path . '';
});

$routingProcessor = new CoreProcessor(__DIR__);
$routingProcessor->pre_processor();

define("ROOT_DIR", $routingProcessor->getRootDir());
define("ROOT_DIR_RELATIVE", $routingProcessor->getRootDirRelative());
define("BASE_URL", $routingProcessor->getBaseUrl());
define("REQUEST", $routingProcessor->getRequest());
define("FULL_REQUEST", $routingProcessor->getFullRequest());


function call_page_function($fun_name)
{
    if (function_exists($fun_name)) {
        call_user_func($fun_name);
    }
}


$urlProcessor = new URLProcessor();
$urlProcessor->findPage(ROOT_DIR . "/pages", REQUEST);
define("URL_PARAMS", $urlProcessor->params);

global $TITLE, $TEMPLATE, $DESCRIPTION, $KEYWORDS, $DEFAULT_PAGE_IMAGE, $user;
$TEMPLATE = ROOT_DIR . '/template/template.php';

if ($urlProcessor->page && file_exists($urlProcessor->page)) {
    include_once $urlProcessor->page . '';
}

if ($TEMPLATE)
    include_once $TEMPLATE . '';


function url_text_encoding($input)
{
    return preg_replace("/\s+/", '-', $input);
}