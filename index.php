<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 12/16/2018
 * Time: 23:00
 */

global $TITLE;
$TITLE = 'ثبت زمان';

include_once 'includes/functions.php';
include_once 'includes/jdf.php';
include_once 'includes/general.php';


function body()
{
    $pdo = get_pdo();

    $errors = array();

//get on-working job
    $on_working = $pdo->query("SELECT `time_log`.*,`job`.`title` FROM `time_log` LEFT JOIN `job` ON `job`.`job_id` = `time_log`.`job_id` WHERE `end` IS NULL ORDER BY `start` DESC LIMIT 1");
    $on_working = $on_working->fetchObject();


    if (isset($_POST['end_job'])) {
        $_POST = input_validate($_POST);
        $on_working->end = time();
        $on_working->duration = calculate_time($on_working->start, $on_working->end);
        $on_working->description = !empty($_POST['description']) ? $_POST['description'] : '';

        $stm = $pdo->prepare("UPDATE time_log  SET `end` = :end, `duration` = :duration, " .
            "`description` = :description " .
            "  WHERE start = :start AND job_id = :job_id;");
        $stm->bindValue(":start", $on_working->start);
        $stm->bindValue(":end", $on_working->end);
        $stm->bindValue(":duration", $on_working->duration);
        $stm->bindValue(":description", $on_working->description);
        $stm->bindValue(":job_id", $on_working->job_id);

        if ($stm->execute())
            header("location: " . $_SERVER['REQUEST_URI']);
    }

    $list_of_jobs = array();
    $stm = $pdo->query("SELECT * FROM `job`");
    while ($job = $stm->fetchObject()) {
        $list_of_jobs[] = $job;
    }

    if (isset($_POST['start_job'])) {
        $_POST = input_validate($_POST);

        if (empty($_POST['job_id']) && empty($_POST['new_job']))
            $errors['job'] = 'فعالیت تعیین نشده است';

        if (count($errors) == 0) {
            if (empty($_POST['job_id'])) {
                $stm = $pdo->prepare("INSERT INTO job (title) VALUES (:title)");
                $stm->execute(array(":title" => $_POST['new_job']));
                $_POST['job_id'] = $pdo->lastInsertId();
                var_dump($_POST['job_id']);
            }

            $stm = $pdo->prepare("INSERT INTO time_log (start,job_id) VALUES (:start,:job_id)");
            $res = $stm->execute(array(
                ":start" => time(),
                ":job_id" => $_POST['job_id']
            ));

            if ($res)
                header("location: " . $_SERVER['REQUEST_URI']);
        }
    }

    ?>

    <div class="container mt-2">
    <?php if (!$on_working): ?>
    <form method="post" class="card">
        <h4 class="card-header">شروع فعالیت</h4>

        <div class="card-body">
            <div class="form-group">
                <label>فعالیت جدید</label>
                <input type="text" name="new_job" class="form-control" placeholder="فعالیت جدید"/>
            </div>

            <div class="form-group">
                <label>انتخاب فعالیت</label>
                <select name="job_id" class="form-control">
                    <option value=""> -- انتخاب فعالیت --</option>

                    <?php foreach ($list_of_jobs as $job) {
                        echo "<option value='{$job->job_id}' >{$job->title}</option>";
                    } ?>
                </select>
            </div>

            <ul class="text-danger">
                <?php foreach ($errors as $error) {
                    echo "<li>{$error}</li>";
                } ?>
            </ul>

        </div>

        <div class="card-footer">
            <input type="submit" name="start_job" value="شروع کار" class="btn btn-primary"/>
        </div>
    </form>
<?php else: ?>
    <form method="post" class="card">
        <h4 class="card-header">پایان دادن به کار در حال اجرا</h4>

        <div class="card-body">
            <table class="table">
                <tr>
                    <th>عنوان کار</th>
                    <td><?php echo $on_working->title ?></td>
                </tr>
                <tr>
                    <th>زمان شروع</th>
                    <td><?php echo jdate("l d F Y ساعت H:i:s", $on_working->start, '', '', 'en'); ?></td>
                </tr>
                <tr>
                    <th>زمان سپری شده</th>
                    <td>
                        <span id="time_gone"></span>
                    </td>
                </tr>
            </table>

            <div class="form-group">
                <label>توضیحات</label>
                <textarea placeholder="توضیحات" name="description" class="form-control"></textarea>
            </div>
        </div>

        <div class="card-footer">
            <input type="submit" name="end_job" value="پایان دادن به کار" class="btn btn-primary"/>
        </div>
    </form>

    <script>
        var s = <?php echo $on_working->start ?>;

        setInterval(function () {
            var e = parseInt(new Date().getTime() / 1000);
            $("#time_gone").text(get_time(s, e));
        }, 1000);

        function get_time(start, end) {
            var seconds = end - start;
            var hours = parseInt(seconds / 3600);
            seconds = seconds % 3600;
            var minutes = parseInt(seconds / 60);
            seconds = seconds % 60;

            return hours + ":" + minutes + ":" + seconds;
        }
    </script>
<?php endif; ?>


    <?php
}