<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 12/17/2018
 * Time: 01:16
 */

include_once 'includes/functions.php';
include_once 'includes/jdf.php';
include_once 'includes/general.php';

$page = !empty($_GET['page']) ? intval($_GET['page']) : 0;
$showInPage = 20;
$offset = $page * $showInPage;

$pdo = get_pdo();
$stm = $pdo->query("SELECT `time_log`.*,`job`.`title` FROM `time_log` " .
    " LEFT JOIN `job` ON `job`.`job_id` = `time_log`.`job_id` " .
    " WHERE `end` IS NOT NULL ORDER BY `start` DESC LIMIT {$showInPage} OFFSET {$offset} ");

$res = array();
while ($r = $stm->fetchObject()) {
    $res[] = $r;
}

?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>تاریخچه کار</title>

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

<div class="table-responsive">
    <table class="table">
        <thead class="badge-dark">
        <tr>
            <th>عنوان کار</th>
            <th>شروع</th>
            <th>پایان</th>
            <th>زمان</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($res as $itm): ?>
            <tr>
                <td><?php echo $itm->title ?></td>
                <td><?php echo jdate("l d F Y ساعت H:i:s", $itm->start); ?></td>
                <td><?php echo jdate("l d F Y ساعت H:i:s", $itm->end); ?></td>
                <td><?php echo $itm->duration ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>