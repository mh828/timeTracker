<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 12/16/2018
 * Time: 23:21
 */

date_default_timezone_set('Asia/Tehran');

/**
 * @param bool $enforce_relation
 * @param bool $force_recreate
 * @return PDO
 */
function get_pdo($enforce_relation = true, $force_recreate = false)
{
    $file_location = str_replace("\\", "/", dirname(__DIR__) . '/database/sqlite.dbs');
    if (!file_exists(dirname($file_location)))
        mkdir(dirname($file_location), 0777, true);

    if ($file_exist = file_exists($file_location) && $force_recreate) {
        unlink($file_location);
    }
    $pdo = new PDO("sqlite:{$file_location}");


    if (!$file_exist || $force_recreate) {
        create_tables($pdo);
    }

    if ($enforce_relation)
        $pdo->exec("PRAGMA foreign_keys = ON;");

    return $pdo;
}


/**
 * @param \PDO $pdo
 */
function create_tables($pdo)
{
    //tables
    $pdo->exec("CREATE TABLE job (`job_id` INTEGER PRIMARY KEY ,`title` TEXT)");
    $pdo->exec("CREATE TABLE time_log (`start` NUMERIC ,`end` numeric, `job_id` integer," .
        " `duration` numeric,`description` TEXT, PRIMARY  KEY (`start`,`job_id`) )");


    //views
    $view = <<<eod
CREATE VIEW view_job_times AS SELECT `job`.*,(SUM(end) - SUM(start)) as time
  FROM `time_log`
  LEFT JOIN `job` on `job`.`job_id` = `time_log`.`job_id`
    GROUP BY `time_log`.`job_id` 
eod;
    $pdo->exec($view);

}

function calculate_time($start, $end, $return_array = false)
{
    $seccond = $end - $start;

    $hours = intval($seccond / 3600);
    $seccond = $seccond % 3600;
    $minute = intval($seccond / 60);
    $seccond = $seccond % 60;

    if ($return_array) {
        return array(
            'hours' => $hours,
            'minutes' => $minute,
            'second' => $seccond
        );
    } else {
        return "{$hours}:{$minute}:{$seccond}";
    }
}

function convert_seconds($seconds)
{

    $hours = intval($seconds / 3600);
    $seconds = $seconds % 3600;
    $minute = intval($seconds / 60);
    $seconds = $seconds % 60;

    return "{$hours}:{$minute}:{$seconds}";
}