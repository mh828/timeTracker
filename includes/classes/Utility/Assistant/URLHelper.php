<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 1/13/2019
 * Time: 15:21
 */

namespace Utility\Assistant;


class URLHelper
{
    public static function URL_TITLE($title)
    {
        $title = preg_replace("/\s/", "-", $title);
        return $title;
    }

}