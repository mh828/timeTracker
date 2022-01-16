<?php
include ROOT_DIR . '/includes/jdf.php';
if (!empty($_REQUEST['generate'])) {
    extract($_REQUEST);
    $start_timestamp = jmktime(0, 0, 0, $start_month, $start_day, $start_year);
    $end_timestamp = jmktime(23, 59, 59, $end_month, $end_day, $end_year);
    $job_id = $_REQUEST['job_id'] ?? null;


    $pdo = get_pdo();
    $queryString = <<<query
SELECT 
date(start, 'unixepoch') as start_date, 
time(start, 'unixepoch') as start_time, 
date(end, 'unixepoch') as end_date,
time(end, 'unixepoch') as end_time,
job.title,
time_log.description 
FROM time_log 
LEFT JOIN  job on job.job_id = time_log.job_id
query;
    if ($job_id)
        $queryString .= " WHERE job_id = {$job_id}";

    $query = $pdo->prepare($queryString);
    $query->execute();


    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="calender.csv"');
    echo "Subject,Start Date,Start Time,End Date,End Time,Description\n";
    while ($obj = $query->fetchObject()) {
        echo "\"{$obj->title}\",\"{$obj->start_date}\",\"{$obj->start_time}\",\"{$obj->end_date}\",\"{$obj->end_time}\",\"{$obj->description}\"";
        echo "\n";
    }
    die();
}

function body()
{
    $pdo = get_pdo();
    $job_id = $_REQUEST['job_id'] ?? null;
    $query_start_date = $pdo->query('SELECT start FROM time_log  order by start asc  limit 1 ');
    $query_start_date->execute();
    $query_start_date = $query_start_date->fetchColumn(0);


    $beginning = $query_start_date;// strtotime('last saturday');
    $start_day = jdate('d', $beginning, '', '', 'en');
    $start_month = jdate('m', $beginning, '', '', 'en');
    $start_year = jdate('Y', $beginning, '', '', 'en');

    $query_end_date = $pdo->query('SELECT end FROM time_log  order by end desc  limit 1 ');
    $query_end_date->execute();
    $query_end_date = $query_end_date->fetchColumn(0);

    $ending = $query_end_date;
    $end_day = jdate('d', $ending, '', '', 'en');
    $end_month = jdate('m', $ending, '', '', 'en');
    $end_year = jdate('Y', $ending, '', '', 'en');


    $jobs = $pdo->prepare('SELECT * FROM job');
    $jobs->execute();

    ?>
    <form class="container mt-3">
        <div class="row">
            <div class="col-sm-4 mt-2">
                <label>فعالیت:</label>
                <select class="form-control" name="job_id">
                    <option value="">همه</option>
                    <?php while ($job = $jobs->fetchObject()): ?>
                        <option value="<?= $job->job_id ?>"><?= $job->title ?></option>
                    <?php endwhile; ?>
                </select>
            </div>


            <div class="col-sm-4 mt-2">
                <label class="sr-only">از تاریخ</label>
                <div class="form-row">
                    <div class="form-group col-sm">
                        <label>روز شروع</label>
                        <select class="form-control" name="start_day">
                            <?php for ($i = 1; $i <= 31; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $start_day == $i ? 'selected="selected"' : '' ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group col-sm">
                        <label>ماه شروع</label>
                        <select class="form-control" name="start_month">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $start_month == $i ? 'selected="selected"' : '' ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group col-sm">
                        <label>سال شروع</label>
                        <input type="tel" pattern="[0-9]+" name="start_year" class="form-control"
                               value="<?php echo $start_year ?>"/>
                    </div>

                </div>
            </div>

            <div class="col-sm-4 mt-2">
                <label class="sr-only">تا تاریخ</label>
                <div class="form-row">
                    <div class="form-group col-sm">
                        <label>روز پایان</label>
                        <select class="form-control" name="end_day">
                            <?php for ($i = 1; $i <= 31; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $end_day == $i ? 'selected="selected"' : '' ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group col-sm">
                        <label>ماه پایان</label>
                        <select class="form-control" name="end_month">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $end_month == $i ? 'selected="selected"' : '' ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group col-sm">
                        <label>سال پایان</label>
                        <input type="tel" pattern="[0-9]+" name="end_year" class="form-control"
                               value="<?php echo $end_year ?>"/>
                    </div>

                </div>
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary" name="generate" value="1">
                ایجاد
            </button>
        </div>
    </form>
    <?php
}
