<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 12/17/2018
 * Time: 01:16
 */

global $TITLE;
$TITLE = "تاریخچه";

include_once ROOT_DIR . '/includes/functions.php';
include_once ROOT_DIR . '/includes/jdf.php';
include_once ROOT_DIR . '/includes/general.php';

function body()
{

    $page = !empty($_GET['page']) ? intval($_GET['page']) : 0;
    $showInPage = 20;
    $offset = $page * $showInPage;

    $pdo = get_pdo();
    $stm = $pdo->query("SELECT * FROM `view_job_times` " .
        " WHERE `time` >= 0 LIMIT {$showInPage} OFFSET {$offset} ");

    $res = array();
    while ($r = $stm->fetchObject()) {
        $res[] = $r;
    }

    ?>

    <div class="table-responsive">
        <table class="table">
            <thead class="badge-dark">
            <tr>
                <th>عنوان کار</th>
                <th>زمان - ثانیه</th>
                <th>زمان - ساعت و دقیقه و ثانیه</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($res as $itm): ?>
                <tr>
                    <td><?php echo $itm->title ?></td>
                    <td><?php echo $itm->time ?></td>
                    <td><?php echo convert_seconds($itm->time) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php
}