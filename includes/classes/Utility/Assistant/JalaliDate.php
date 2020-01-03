<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 12/5/2018
 * Time: 16:10
 */

namespace Utility\Assistant;
include_once ROOT_DIR . '/includes/jdf.php';


class JalaliDate
{
    const DATE_VALIDATION_PATTERN = "/^([0-9]{4})\/([0-9]|0[0-9]|1[0-2])\/([0-9]|[0-2][0-9]|3[0-1])$/";
    const TIME_VALIDATION_PATTERN = "/^([0-9]|[0-1][0-9]|2[0-3]):([0-9]|[0-5][0-9])$/";

    public function date_jalai_to_gregorian($date, $delimiter = '-')
    {
        $result = false;
        if (preg_match(self::DATE_VALIDATION_PATTERN, $date)) {
            $date = explode("/", $date);
            $result = date("Y{$delimiter}m{$delimiter}d", jmktime(0, 0, 0, $date[1], $date[2], $date[0]));
        }

        return $result;
    }

    public function days_name($int_zero_based_day)
    {
        switch ($int_zero_based_day) {
            case 0:
                return 'شنبه';
            case 1:
                return 'یک شنبه';
            case 2:
                return 'دوشنبه';
            case 3:
                return 'سه شنبه';
            case 4:
                return 'چهارشنبه';
            case 5:
                return 'پنجشنبه';
            case 6:
                return 'جمعه';
            default:
                return '';
        }
    }

    public static function get_date($date)
    {
        $time = strtotime($date);

        if (date("Y-m-d") == date("Y-m-d", $time))
            return "امروز ساعت " . date("H:i:s", $time);
        else if (date("Y-m-d", strtotime("-1 day")) == date("Y-m-d", $time))
            return "دیروز ساعت " . date("H:i:s", $time);
        else
            return jdate("l d F Y ساعت H:i:s", $time, '', '', 'en');
    }

    /**
     * @param $string : input format must be YYYY/mm/dd HH:ii[:ss]
     * @return false|int
     */
    public static function convertStringToTime($string){
        $split = preg_split("/\s+/",$string);
        //0 => date
        //1 => time
        if(empty($split[0]) || empty($split[1]))
            return false;

        $date = explode("/",$split[0]);
        //0 => Year
        //1 => month
        //2 => day

        $time = explode(":",$split[1]);
        //0 => hour
        //1 => minute
        //[2] => second ; optional

        if(empty($date[0]) || empty($date[1]) || empty($date[2]) || empty($time[0])  || empty($time[1]))
            return false;
        return jmktime($time[0],$time[1],isset($time[2])? $time[2] : 0,
            $date[1],$date[2],$date[0]);
    }
}