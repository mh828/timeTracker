<?php

Statics::addBundle(Statics::BUNDLE_JDF);
function body()
{
    $timelog_rowid = $_GET['rowid'] ?? null;
    $pdo = get_pdo();
    $query = 'SELECT time_log.*, job.title as job_title FROM time_log ' .
        'LEFT JOIN job on job.job_id = time_log.job_id' .
        ' WHERE time_log.rowid = :rowid';
    $timelog = $pdo->prepare($query);
    $timelog->execute([':rowid' => $timelog_rowid]);
    $timelog = $timelog->fetchObject();

    if (!empty($_POST['confirm-delete'])) {
        $pdo->query('DELETE FROM time_log WHERE rowid = ?')->execute([$timelog_rowid]);
        header("location: " . BASE_URL . '/list2');
    }

    if ($timelog) {
        ?>
        <form method="post" class="container">
            <h3 class="text-danger">آیا مایل به حذف تاریخچه با اطلاعات زیر هستید؟</h3>

            <table class="table my-2">
                <tr>
                    <th>عنوان فعالیت:</th>
                    <td><?php echo $timelog->job_title ?></td>
                </tr>
                <tr>
                    <th>تاریخ و زمان شروع:</th>
                    <td><?php echo jdate('l d F Y ساعت H:i:s', $timelog->start) ?></td>
                </tr>
                <tr>
                    <th>تاری و زمان پایان:</th>
                    <td><?php echo jdate('l d F Y ساعت H:i:s', $timelog->end) ?></td>
                </tr>
                <tr>
                    <th>مدت زمان:</th>
                    <td><?php echo $timelog->duration ?></td>
                </tr>
                <tr>
                    <th>توضیحات:</th>
                    <td><?php echo $timelog->description ?></td>
                </tr>
            </table>

            <div dir="ltr">
                <input type="submit" name="confirm-delete" class="btn btn-danger" value="بله، حذف کن"/>
                <a href="<?php echo BASE_URL . "/list2" ?>" class="btn btn-primary">
                    انصراف و برگشت
                </a>
            </div>
        </form>
        <?php
    }
}