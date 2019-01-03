<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 1/3/2019
 * Time: 22:16
 */

include_once 'includes/general.php';

class Statics
{
    const BUNDLE_JDF = "includes/jdf.php";

    public static function get_pdo()
    {
        return get_pdo();
    }


    public static function addBundle($bundle)
    {
        include_once @$bundle . '';
    }
}