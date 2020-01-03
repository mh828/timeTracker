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
    $file_location = str_replace("\\", "/", ROOT_DIR . '/database/sqlite.dbs');
    if (!file_exists(dirname($file_location)))
        mkdir(dirname($file_location), 0777, true);

    $file_exist = file_exists($file_location);
    if ($file_exist && $force_recreate) {
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
    return $pdo->exec(sqliteDDL());
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

    $view = <<<eod
    CREATE VIEW view_timelog AS SELECT start,
            datetime(start,'unixepoch','localtime') as start_date,
           [end],
            datetime([end],'unixepoch','localtime') as end_date,
           job_id,
           duration,
           description
      FROM time_log;
eod;
    $pdo->exec($view);

    $view = <<<eod
    CREATE VIEW view_job_daily_sum AS
    SELECT date([start],'unixepoch','localtime') as start_date,
            date([end],'unixepoch','localtime') as end_date,
           job_id,
           SUM([end]) - SUM(start) as duration
      FROM time_log
      GROUP BY start_date,job_id
eod;
    $pdo->exec($view);

}

function sqliteDDL()
{
    return <<<end
--
-- File generated with SQLiteStudio v3.2.1 on Fri Jan 3 07:58:47 2020
--
-- Text encoding used: System
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: job
CREATE TABLE job (
    job_id INTEGER PRIMARY KEY,
    title  TEXT
);


-- Table: time_log
CREATE TABLE time_log (
    start       NUMERIC,
    [end]       NUMERIC,
    job_id      INTEGER,
    duration    NUMERIC,
    description TEXT,
    PRIMARY KEY (
        start,
        job_id
    ),
    FOREIGN KEY (
        job_id
    )
    REFERENCES job (job_id) ON DELETE CASCADE
                            ON UPDATE CASCADE
);


-- Index: time_index
CREATE INDEX time_index ON time_log (
    start ASC,
    "end" ASC
);


-- View: view_daily_sum_jobs
CREATE VIEW view_daily_sum_jobs AS
    SELECT job_id,
           date(start, 'unixepoch', 'localtime') AS date,
           SUM([end]) - SUM(start) AS time
      FROM view_timelog
     WHERE [end] IS NOT NULL
     GROUP BY date,
              job_id;


-- View: view_job_daily
CREATE VIEW view_job_daily AS
    SELECT date(start, 'unixepoch', 'localtime') AS date,
           SUM([end]) - SUM(start) AS seconds
      FROM time_log
     WHERE [end] IS NOT NULL
     GROUP BY date;


-- View: view_job_daily_sum
CREATE VIEW view_job_daily_sum AS
    SELECT date(start, 'unixepoch', 'localtime') AS start_date,
           date([end], 'unixepoch', 'localtime') AS end_date,
           job.job_id,
           job.title,
           SUM([end]) - SUM(start) AS duration
      FROM time_log
           LEFT JOIN
           job ON job.job_id = time_log.job_id
     WHERE [end] IS NOT NULL
     GROUP BY start_date,
              time_log.job_id;


-- View: view_job_times
CREATE VIEW view_job_times AS
    SELECT job.*,
           (SUM([end]) - SUM(start) ) AS time
      FROM time_log
           LEFT JOIN
           job ON job.job_id = time_log.job_id
     GROUP BY time_log.job_id;


-- View: view_timelog
CREATE VIEW view_timelog AS
    SELECT start,
           datetime(start, 'unixepoch', 'localtime') AS start_date,
           [end],
           datetime([end], 'unixepoch', 'localtime') AS end_date,
           job_id,
           duration,
           description
      FROM time_log;


COMMIT TRANSACTION;
PRAGMA foreign_keys = on;

end;
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