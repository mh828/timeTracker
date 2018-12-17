<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 12/16/2018
 * Time: 23:00
 */

include_once 'includes/functions.php';
include_once 'includes/jdf.php';
include_once 'includes/general.php';

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

?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ثبت زمان های کاری</title>

    <script src="resources/freamworks/jquery-3.3.1.min.js"></script>
    <script src="resources/freamworks/popper.js"></script>
    <script src="resources/freamworks/bootstrapRTL/bootstrap.min.js"></script>
    <link rel="stylesheet" href="resources/freamworks/bootstrapRTL/bootstrap.min.css">

    <link rel="stylesheet" href="resources/styles/index.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">

            <li class="nav-item">
                <a class="nav-link" href="index.php">ثبت</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="list.php">تاریخچه</a>
            </li>

            <!--<li class="nav-item active">
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Dropdown
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#">Disabled</a>
            </li>-->
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>

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
</div>

</body>
</html>