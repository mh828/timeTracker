--
-- File generated with SQLiteStudio v3.2.1 on Fri Dec 4 11:10:14 2020
--
-- Text encoding used: System
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: job
DROP TABLE IF EXISTS job;

CREATE TABLE job (
    job_id INTEGER PRIMARY KEY,
    title  TEXT
);


-- Table: time_log
DROP TABLE IF EXISTS time_log;

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
DROP INDEX IF EXISTS time_index;

CREATE INDEX time_index ON time_log (
    start ASC,
    "end" ASC
);


-- View: view_daily_sum_jobs
DROP VIEW IF EXISTS view_daily_sum_jobs;
CREATE VIEW view_daily_sum_jobs AS
    SELECT job_id,
           date(start, 'unixepoch', 'localtime') AS date,
           SUM([end]) - SUM(start) AS time
      FROM view_timelog
     WHERE [end] IS NOT NULL
     GROUP BY date,
              job_id;


-- View: view_job_daily
DROP VIEW IF EXISTS view_job_daily;
CREATE VIEW view_job_daily AS
    SELECT date(start, 'unixepoch', 'localtime') AS date,
           SUM([end]) - SUM(start) AS seconds
      FROM time_log
     WHERE [end] IS NOT NULL
     GROUP BY date;


-- View: view_job_daily_sum
DROP VIEW IF EXISTS view_job_daily_sum;
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
DROP VIEW IF EXISTS view_job_times;
CREATE VIEW view_job_times AS
    SELECT job.*,
           (SUM([end]) - SUM(start) ) AS time
      FROM time_log
           LEFT JOIN
           job ON job.job_id = time_log.job_id
     GROUP BY time_log.job_id;


-- View: view_timelog
DROP VIEW IF EXISTS view_timelog;
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
