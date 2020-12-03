select 
    job.job_id,
    job.title,
    SUM(time_log.end) - sum(time_log.start) as sum 
from time_log
LEFT JOIN job on job.job_id = time_log.job_id
where time_log.start >= :start and time_log.end <= :end
group by time_log.job_id 
