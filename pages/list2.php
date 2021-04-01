<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 1/3/2019
 * Time: 22:18
 */

Statics::addBundle(Statics::BUNDLE_JDF);
function body()
{

    $showInPage = isset($_GET['showInPage']) ? $_GET['showInPage'] : 10;
    $page = isset($_GET['page']) ? $_GET['page'] - 1 : 0;
    $job_id = isset($_GET['job_id']) ? $_GET['job_id'] : null;

    $timeLogs = new \DBS\Views\ViewTimeLog($showInPage, $page);
    $timeLogs->filter_job_id($job_id)
        ->hide_undone(empty($_GET['all']))
        ->do_query();

    $paging_controller = new \Utility\Assistant\Paging($timeLogs);

    ?>
    <div class="container-fluid">
        <div class="table-responsive">

            <table class="table">
                <thead class="badge-dark">
                <tr>
                    <th>فعالیت</th>
                    <th>مدت زمان</th>
                    <th>شروع</th>
                    <th>پایان</th>
                    <th>&</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($timeLogs as $log) : ?>
                    <tr>
                        <td>
                            <a href="<?php echo BASE_URL . "/list2?job_id=" . $log->job_id; ?>"><?php echo $log->job_title ?></a>
                        </td>
                        <td><?php echo !empty($log->end) ? $log->duration : '--- درحال اجرا ---' ?></td>
                        <td><?php echo jdate("l d F Y ساعت H:i:s", $log->start) ?></td>
                        <td><?php echo !empty($log->end) ? jdate("l d F Y ساعت H:i:s", $log->end) : '--- درحال اجرا ---' ?></td>

                        <td>
                            <a href="<?php echo BASE_URL . "/forms/log/add?start={$log->start}&job_id={$log->job_id}"; ?>">ویرایش</a>
                            |
                            <a href="<?php echo BASE_URL . "/forms/log/delete?rowid={$log->rowid}" ?>"
                               class="text-danger">حذف</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        </div>

        <?php $paging_controller->render_handler(true, $_GET); ?>
    </div>
    <?php
}