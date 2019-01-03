<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 12/5/2018
 * Time: 16:10
 */

namespace Utility\Assistant;
include_once 'includes/jdf.php';


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
}