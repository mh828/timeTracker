<?php
include ROOT_DIR . '/includes/jdf.php';

global $TITLE;
$TITLE = 'گزارش دوره‌ای شغل';

function body()
{
    $job_id = $_REQUEST['job_id'] ?? null;
    $pdo = get_pdo();

    if ($job_id) {
        $beginning = $pdo->query('SELECT start FROM time_log  order by start asc  limit 1 ');
        $beginning->execute();
        $beginning = $beginning->fetchColumn(0);

        $start_day = jdate('d', $beginning, '', '', 'en');
        $start_month = jdate('m', $beginning, '', '', 'en');
        $start_year = jdate('Y', $beginning, '', '', 'en');

        $ending = $pdo->query('SELECT end FROM time_log  order by end desc  limit 1 ');
        $ending->execute();
        $ending = $ending->fetchColumn(0);

        $end_day = jdate('d', $ending, '', '', 'en');
        $end_month = jdate('m', $ending, '', '', 'en');
        $end_year = jdate('Y', $ending, '', '', 'en');

        extract($_POST);
        $start_timestamp = jmktime(0, 0, 0, $start_month, $start_day, $start_year);
        $end_timestamp = jmktime(23, 59, 59, $end_month, $end_day, $end_year);

        $job = $pdo->query("select * from job where job_id = ?");
        $job->execute([$job_id]);
        $job = $job->fetchObject();

        $query = $pdo->prepare('SELECT * FROM time_log WHERE job_id = :job_id AND start >= :start AND end <= :end');
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
        ?>
        <div class="container">
            <div>
                <span class="mr-1">شغل انتخابی:</span>
                <span class="font-weight-bolder"><?php echo $job->title; ?></span>

                <span class="mx-2">
                    <a href="<?php echo BASE_URL . "/reports/job_interval"; ?>">
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

                <div>
                    <input type="submit" class="btn btn-primary" value="دریافت گزارش"/>
                </div>
            </form>

            <hr/>
            <div>
                <b class="font-weight-bolder mr-1">مجموع ساعت: </b>
                <span class="h5"><?php echo convert_seconds($sum); ?></span>
            </div>
            <div class="container">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="thead-dark">
                        <tr>
                            <th>روز</th>
                            <th>زمان شروع</th>
                            <th>زمان پایان</th>
                            <th>مدت زمان</th>
                            <th>توضیحات</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php while ($r = $query->fetchObject()) : ?>
                            <tr>
                                <td>

                                </td>
                                <td><?php echo jdate("(l) d F Y - H:i:s", $r->start) ?></td>
                                <td><?php echo jdate("(l) d F Y - H:i:s", $r->end) ?></td>
                                <td><?php echo $r->duration; ?></td>
                                <td><?php echo $r->description; ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    } else {
        $jobs = $pdo->prepare('SELECT * FROM job');
        $jobs->execute();
        ?>
        <div class="container">
            <h3>ابتدا یک شغل را انتخاب کنید</h3>
            <div class="form-group">
                <input type="search" class="form-control" oninput="searchIn(this.value)" placeholder="جستجو"/>
            </div>
            <nav class="nav justify-content-center" id="job-navigation">
                <?php while ($row = $jobs->fetchObject()) : ?>
                    <li class="nav-item m-1" data-title="<?php echo $row->title ?>">
                        <a href="<?php echo BASE_URL . "/reports/job_interval?job_id={$row->job_id}"; ?>"
                           class="nav-link p-1  bg-light rounded shadow-sm border rounded">
                            <?php echo $row->title; ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </nav>
        </div>
        <?php
    }
}

function script()
{
    ?>
    <script>
        function searchIn(value) {
            $('#job-navigation .nav-item[data-title]').each((ind, elm) => {
                if ($(elm).attr('data-title').startsWith(value))
                    $(elm).show();
                else
                    $(elm).hide();
            })
        }
    </script>
    <?php
}