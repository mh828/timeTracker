<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 12/17/2018
 * Time: 15:02
 */

global $TITLE;
$TITLE = "گزارش مجموع کارکرد روزانه";

include_once ROOT_DIR . '/includes/general.php';
include_once ROOT_DIR . '/includes/jdf.php';

function body()
{
    $pdo = get_pdo();
    $query = $pdo->query("SELECT * FROM `view_job_daily` ORDER BY `date` DESC");
    $rows = array();
    while ($r = $query->fetchObject()) {
        $r->date = jdate("l d F Y", strtotime($r->date));
        $r->work = convert_seconds($r->seconds);
        $rows[] = $r;
    }
    ?>
    <div class="container">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr class="badge-dark">
                    <th class="text-center">تاریخ</th>
                    <th class="text-center">زمان کار (ثانیه)</th>
                    <th class="text-center">ساعت</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($rows as $r) : ?>
                    <tr>
                        <td><?php echo $r->date; ?></td>
                        <td><?php echo $r->seconds; ?></td>
                        <td><?php echo $r->work; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}