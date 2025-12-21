<?php
/**
 * @var  PDO $pdo
 * @var int $job_id
 * */
$query_start_date = $pdo->query('SELECT start FROM time_log WHERE job_id = ?  order by start asc  limit 1 ');
$query_start_date->execute([$job_id]);
$query_start_date = $query_start_date->fetchColumn(0);

$beginning = strtotime('last saturday');
$start_day = jdate('d', $beginning, '', '', 'en');
$start_month = jdate('m', $beginning, '', '', 'en');
$start_year = jdate('Y', $beginning, '', '', 'en');

$query_end_date = $pdo->query('SELECT end FROM time_log  WHERE job_id = ?  order by end desc  limit 1 ');
$query_end_date->execute([$job_id]);
$query_end_date = $query_end_date->fetchColumn(0);

$ending = time();
$end_day = jdate('d', $ending, '', '', 'en');
$end_month = jdate('m', $ending, '', '', 'en');
$end_year = jdate('Y', $ending, '', '', 'en');

extract($_POST);
$start_timestamp = jmktime(0, 0, 0, $start_month, $start_day, $start_year);
$end_timestamp = jmktime(23, 59, 59, $end_month, $end_day, $end_year);

$job = $pdo->query("select * from job where job_id = ?");
$job->execute([$job_id]);
$job = $job->fetchObject();

$query = "start,end,date(start,'unixepoch','localtime') as startdata, sum(end) - sum(start) as sum";
$query = 'SELECT ' . $query . ' FROM time_log ';
$query .= ' WHERE job_id = :job_id AND start >= :start AND end <= :end';
$query .= " group by startdata ";
$query .= " ORDER BY end {$order} ";

$query = $pdo->prepare($query);
$query->bindValue(':job_id', $job_id);
$query->bindValue(':start', $start_timestamp);
$query->bindValue(':end', $end_timestamp);
$query->execute();

$sum = $pdo->prepare('SELECT SUM(end) - SUM(start) as sum from time_log WHERE job_id = :job_id AND start >= :start AND end <= :end');
$sum->bindValue(':job_id', $job_id);
$sum->bindValue(':start', $start_timestamp);
$sum->bindValue(':end', $end_timestamp);
$sum->execute();
$sum = $sum->fetchColumn(0);

$dont_show_day_column = !empty($_POST['dont_show_day_column']);

?>
<div class="container">
    <div class="d-print-none">
        <div class="mb-3 border-bottom border-light container py-2">
            <span class="mr-1">شغل انتخابی:</span>
            <span class="font-weight-bolder"><?php echo $job->title; ?></span>

            <span class="mx-2">
                    <a href="<?php echo BASE_URL . "/reports/job_interval"; ?>" class="btn btn-primary">
                        انتخاب شغل دیگر
                    </a>
                </span>
        </div>

        <form method="post">
            <div class="form-row">
                <div class="form-group col-md">
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

                <div class="form-group col-md">
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

            <div class="form-row">
                <div class="form-group col-md">
                    <div class="form-check-inline">
                        <input type="radio" name="order"
                               value="ASC" <?php echo $order === 'ASC' ? 'checked' : ''; ?>
                               class="form-check-input" id="order-checkbox-asc">
                        <label for="order-checkbox-asc" class="form-check-label">
                            صعودی
                        </label>
                    </div>
                    <div class="form-check-inline">
                        <input type="radio" name="order"
                               value="DESC" <?php echo $order === 'DESC' ? 'checked' : ''; ?>
                               class="form-check-input" id="order-checkbox-asc">
                        <label for="order-checkbox-asc" class="form-check-label">
                            نزولی
                        </label>
                    </div>
                </div>

                <div class="form-group col-md">
                    <label>فرمت نمایش تاریخ</label>

                    <div>
                        <?php foreach ($date_time_formats as $key => $format) : ?>
                            <div class="form-check">
                                <input id="date_time_format_<?php echo $key ?>" type="radio"
                                       name="date_time_format"
                                        <?php echo $format === $date_time_format ? 'checked' : '' ?>
                                       value="<?php echo $format ?>"
                                       class="form-check-input"/>
                                <label class="form-check-label" for="date_time_format_<?php echo $key ?>">
                                    <?php echo jdate($format, '', '', '', 'en') ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-check">
                    <input type="checkbox" name="dont_show_day_column" value="1"
                            <?php echo $dont_show_day_column ? 'checked' : ''; ?>
                           class="form-check-input" id="show-day-column-checkbox">
                    <label for="show-day-column-checkbox" class="form-check-label">
                        عدم نمایش ستون روز
                    </label>
                </div>
            </div>

            <div>
                <input type="submit" class="btn btn-primary" value="دریافت گزارش"/>
            </div>
        </form>

        <hr/>
    </div>

    <div>
        <table>
            <tr>
                <th class="pr-2">بازه گزارش از:</th>
                <td>
                    <?php echo jdate('(l) d F Y', $start_timestamp, '', '', 'en'); ?>
                </td>
                <th class="px-2">تا:</th>
                <td>
                    <?php echo jdate('(l) d F Y', $end_timestamp, '', '', 'en'); ?>
                </td>
            </tr>
            <tr>
                <th class="pr-2">بازه فعالیت از :</th>
                <td>
                    <?php echo jdate('(l) d F Y', $query_start_date, '', '', 'en'); ?>
                </td>
                <th class="px-2">تا:</th>
                <td>
                    <?php echo jdate('(l) d F Y', $query_end_date, '', '', 'en'); ?>
                </td>
            </tr>
            <tr class="border-top">
                <th class="pr-2">مجموع ساعت:</th>
                <td>
                    <?php echo convert_seconds($sum); ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="container">
        <div class="table-responsive">
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <?php if (!$dont_show_day_column): ?>
                        <th>روز</th>
                    <?php endif; ?>
                    <th>مدت زمان</th>
                </tr>
                </thead>

                <tbody>
                <?php while ($r = $query->fetchObject()) : ?>
                    <tr>
                        <?php if (!$dont_show_day_column) : ?>
                            <td>
                                <?php echo jdate("l d F Y", $r->start) ?>
                            </td>
                        <?php endif; ?>
                        <td><?php echo convert_seconds($r->sum); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
