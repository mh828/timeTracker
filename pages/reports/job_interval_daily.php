<?php
include ROOT_DIR . '/includes/jdf.php';

global $TITLE;
$TITLE = 'گزارش دوره‌ای شغل';

function body()
{
    $job_id = $_REQUEST['job_id'] ?? null;
    $order = !empty($_REQUEST['order']) ? $_REQUEST['order'] : 'DESC';
    $pdo = get_pdo();

    $date_time_formats = [
        "H:i:s",
        "(l) d F Y - H:i:s",
        "l d F Y - H:i:s",
        "Y-m-d H:i:s",
        "Y/m/d H:i:s"
    ];
    $date_time_format = $date_time_formats[0];

    if ($job_id) {
        include __DIR__.'/../../views/job_interval_daily_report_body.php';
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
                        <a href="<?php echo BASE_URL . "/reports/job_interval_daily?job_id={$row->job_id}"; ?>"
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