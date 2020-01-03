<?php

$rurl = $_SERVER['REQUEST_URI'];
if(preg_match("/\.(?!php)/",$rurl)){
    return false;
}


include_once __DIR__ . '/router.php';
return true;