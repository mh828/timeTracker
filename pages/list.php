<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 12/17/2018
 * Time: 01:16
 */

global $TITLE;
$TITLE = "تاریخچه";

include_once ROOT_DIR. '/includes/functions.php';
include_once ROOT_DIR . '/includes/jdf.php';
include_once ROOT_DIR . '/includes/general.php';

function body()
{

    $page = !empty($_GET['page']) ? intval($_GET['page']) : 0;
    $showInPage = 20;
    $offset = $page * $showInPage;

    $pdo = get_pdo();
    $stm = $pdo->query("SELECT `time_log`.`rowid`, `time_log`.*,`job`.`title` FROM `time_log` " .
        " LEFT JOIN `job` ON `job`.`job_id` = `time_log`.`job_id` " .
        " WHERE `end` IS NOT NULL ORDER BY `start` DESC LIMIT {$showInPage} OFFSET {$offset} ");

    $res = array();
    while ($r = $stm->fetchObject()) {
        $res[] = $r;
    }

    ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead class="badge-dark">
            <tr>
                <th>#</th>
                <th>عنوان کار</th>
                <th>شروع</th>
                <th>پایان</th>
                <th>زمان</th>
                <th>توضیحات</th>
                <th>&</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($res as $itm): ?>
                <tr>
                    <td><?php echo $itm->rowid; ?></td>
                    <td><?php echo $itm->title ?></td>
                    <td><?php echo jdate("l d F Y ساعت H:i:s", $itm->start); ?></td>
                    <td><?php echo jdate("l d F Y ساعت H:i:s", $itm->end); ?></td>
                    <td><?php echo $itm->duration ?></td>
                    <td style="white-space: pre;"><?php echo $itm->description ?></td>
                    <td>

                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php
}