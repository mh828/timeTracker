<?php
global  $TITLE;
?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo !empty($TITLE) ? $TITLE . " | " : '' ?>مدیریت زمان</title>


    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/resources/freamworks/bootstrap-4.2.1/css/bootstrap.min.css">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/resources/styles/index.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
    <a class="navbar-brand" href="#">زمانبندی</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">

            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL ?>/register">ثبت</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL ?>/register-list">ثبت لیست</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL ?>/list2">تاریخچه</a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="reportsDropDown" role="button" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    گزارشات
                </a>
                <div class="dropdown-menu" aria-labelledby="reportsDropDown">
                    <a class="dropdown-item" href="<?php echo BASE_URL ?>/reports/daily">گزارش روزانه</a>
                    <a class="dropdown-item" href="<?php echo BASE_URL ?>/reports/daily_total">گزارش مجموع کارکرد
                        روزانه</a>
                    <a class="dropdown-item" href="<?php echo BASE_URL ?>/reports/job_overview">مجموع زمان کار</a>
                    <a class="dropdown-item" href="<?php echo BASE_URL ?>/reports/interval">دوره ای</a>
                    <a class="dropdown-item" href="<?php echo BASE_URL ?>/reports/job_interval">دوره ای شغل</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
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

<div class="mt-2">
    <?php call_page_function('body'); ?>
</div>

<script src="<?php echo BASE_URL; ?>/resources/freamworks/jquery-3.5.1.min.js"></script>
<script src="<?php echo BASE_URL; ?>/resources/freamworks/popper.min.js"></script>
<script src="<?php echo BASE_URL; ?>/resources/freamworks/bootstrap-4.2.1/js/bootstrap.min.js"></script>

<?php call_page_function('script'); ?>
</body>
</html>