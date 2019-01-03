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

    /**
     * @param $string
     * @return false|int
     */
    public static function convert_jalali_to_time($string)
    {
        $res = false;
        $string = preg_split("/\s+/", $string);
        if (count($string) == 2) {
            $date = preg_split("/\//", $string[0]);
            $time = preg_split("/:/", $string[1]);

            if (count($date) == 3 && count($time) >= 2)
                $res = jmktime($time[0], $time[1], (isset($time[2]) ? $time[2] : 0), $date[1], $date[2], $date[0]);
        }

        return $res;
    }
}