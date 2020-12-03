<?php
global $TITLE;
$TITLE = 'گزارش بازه‌ای';

include ROOT_DIR . '/includes/jdf.php';

function body()
{
    $start_day = jdate('d', '', '', '', 'en');
    $start_month = jdate('m', '', '', '', 'en');
    $start_year = jdate('Y', '', '', '', 'en');

    $end_day = jdate('d', '', '', '', 'en');
    $end_month = jdate('m', '', '', '', 'en');
    $end_year = jdate('Y', '', '', '', 'en');

    extract($_POST);
    $start_timestamp = jmktime(0, 0, 0, $start_month, $start_day, $start_year);
    $end_timestamp = jmktime(23, 59, 59, $end_month, $end_day, $end_year);

    $pdo = get_pdo();
    $queryString = file_get_contents(ROOT_DIR . '/queries/intervalTimeLog.sql');
    $query = $pdo->prepare($queryString);
    $query->bindValue(':start', $start_timestamp);
    $query->bindValue(':end', $end_timestamp);
    $query->execute();


    include 'interval.template.php';
}