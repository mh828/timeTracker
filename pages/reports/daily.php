<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 12/17/2018
 * Time: 15:02
 */

global $TITLE;
$TITLE = "گزارش روزانه";

include_once ROOT_DIR . '/includes/general.php';
include_once ROOT_DIR . '/includes/jdf.php';

function body()
{
    $rows = retrieve_daily_report();
    ?>
    <div class="container">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr class="badge-dark">
                    <th class="text-center">فعالیت</th>
                    <th class="text-center">زمان کار (ثانیه)</th>
                    <th class="text-center">ساعت</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($rows as $rk => $rv) {
                    echo "<tr><th class='badge-secondary text-center' colspan='3'>{$rk}</th></tr>";
                    foreach ($rv as $itm) {
                        ?>
                        <tr>
                            <td><?php echo $itm->title; ?></td>
                            <td><?php echo $itm->time; ?></td>
                            <td><?php echo convert_seconds($itm->time); ?></td>
                        </tr>
                        <?php
                    }
                } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

function retrieve_daily_report()
{
    $pdo = get_pdo();

    $start = date("Y-m-d", strtotime("-9 days"));
    $end = date("Y-m-d");

    $res = array();

    while ($start <= $end) {
        $start_duration = strtotime($start . " 00:00:00");
        $end_duration = strtotime($start . " 23:59:59");

        $tmp = array();
        $query = <<<EOS
SELECT
        job.title,
       description,
       (SUM(end) - SUM(start)) as time
  FROM time_log
LEFT JOIN `job` ON job.job_id = time_log.job_id
  WHERE start BETWEEN :start AND :end
  AND `end` IS NOT NULL
  GROUP BY time_log.job_id
EOS;
        $query = $pdo->prepare($query);
        $query->execute(array(
            ":start" => $start_duration,
            ":end" => $end_duration));

        while ($r = $query->fetchObject()) {
            $tmp[] = $r;
        }

        if ($tmp)
            $res[jdate("l d F Y", $start_duration, '', '', 'en')] = $tmp;
        $start = date("Y-m-d", strtotime($start . " +1 days"));
    }

    return $res;
}