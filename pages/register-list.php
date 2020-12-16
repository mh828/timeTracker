<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 12/16/2018
 * Time: 23:00
 */

global $TITLE;
$TITLE = 'ثبت زمان';

include_once ROOT_DIR . '/includes/functions.php';
include_once ROOT_DIR . '/includes/jdf.php';
include_once ROOT_DIR . '/includes/general.php';


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
        //$on_working->description = !empty($_POST['description']) ? $_POST['description'] : '';

        $stm = $pdo->prepare("UPDATE time_log  SET `end` = :end, `duration` = :duration, " .
            "`description` = :description " .
            "  WHERE start = :start AND job_id = :job_id;");
        $stm->bindValue(":start", $on_working->start);
        $stm->bindValue(":end", $on_working->end);
        $stm->bindValue(":duration", $on_working->duration);
        $stm->bindValue(":description", trim($on_working->description . PHP_EOL . $_POST['description']));
        $stm->bindValue(":job_id", $on_working->job_id);

        if ($stm->execute())
            header("location: " . $_SERVER['REQUEST_URI']);
    }
    else if(isset($_POST['append_description'])){
        $_POST = input_validate($_POST);
        if(!empty($_POST['description'])){
            $query = $pdo->prepare("UPDATE time_log SET description  =  :description WHERE start = :start AND job_id = :job_id");
            $query->bindValue(':description',trim($on_working->description . PHP_EOL . $_POST['description']) );
            $query->bindValue(":start", $on_working->start);;
            $query->bindValue(":job_id", $on_working->job_id);
            $query->execute();
        }
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


    $latestDidJobs = "select job.job_id,job.title,count(time_log.duration) as 'count',  max(time_log.end) as 'end' from job ".
    " left join time_log on time_log.job_id = job.job_id ".
    " where time_log.end is not null ".
    " group by job.job_id ".
    " order by time_log.end DESC LIMIT 5";
    $latestDidJobs = $pdo->prepare($latestDidJobs);
    $latestDidJobs->execute();
    ?>

    <div class="container mt-2">
<?php if (!$on_working): ?>
    <form method="post" class="card" id="start-activity-form">
        <h4 class="card-header">شروع فعالیت</h4>

        <div class="card-body">
            <div class="form-group">
                <label>فعالیت جدید</label>
                <div class="input-group">
                    <input type="text" name="new_job" class="form-control" placeholder="فعالیت جدید"/>
                    <div class="input-group-append">
                        <input type="submit" name="start_job" value="شروع کار" class="btn btn-primary"/>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>انتخاب فعالیت</label>

                <input type="search" class="form-control" oninput="searchIn(this.value)" placeholder="جستجو"/>
                <input type="hidden" name="job_id" value="" />

                <div class="d-flex flex-wrap" id="job-buttons">
                    <?php foreach ($list_of_jobs as $job) : ?>
                        <button type="button" data-title="<?php echo $job->title ?>"
                        onclick="onJobStart(event,'<?php echo $job->job_id ?>')"
                        class="btn btn-light m-1 shadow-sm">
                            <?php echo $job->title ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <hr />
                <h3>آخرین کارهای انجام شده</h3>
                <div class="d-flex flex-column align-items-start flex-wrap" id="job-buttons">
                    <?php while ( $job = $latestDidJobs->fetchObject()) : ?>
                        <button type="button" data-title="<?php echo $job->title ?>"
                        onclick="onJobStart(event,'<?php echo $job->job_id ?>')"
                        class="btn btn-light m-1 shadow-sm">
                            <span><?php echo $job->title ?></span>
                            <span class="badge badge-info ml-2"><?php echo $job->count ?></span>
                        </button>
                    <?php endwhile; ?>
                </div>
            </div>

            <ul class="text-danger">
                <?php foreach ($errors as $error) {
                    echo "<li>{$error}</li>";
                } ?>
            </ul>

        </div>

        <div class="card-footer">

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
            <input type="submit" name="append_description" value="اضافه کردن توضیحات" class="btn btn-info"/>
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

function script()
{
    ?>
    <script>
    function searchIn(value){
        $('#job-buttons button[data-title]').each((i,e)=>{
            if($(e).attr('data-title').startsWith(value))
                $(e).show();
            else
                $(e).hide();
        })
    }

    function onJobStart(e,job_id){
        const form = document.getElementById('start-activity-form');
        form.job_id.value = job_id;
        $(form).append('<input type="hidden" name="start_job" />');
        form.submit();
    }
    </script>
    <?php
}