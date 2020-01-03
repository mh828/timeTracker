<?php


function body()
{
    $jobTimes = new \DBS\Views\ViewJobTimes(0);
    $jobTimes->do_query();
    ?>
    <div class="container">

        <div class="table-responsive">

            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th>کار</th>
                    <th>زمان به ثانیه</th>
                    <th>زمان به دقیقه</th>
                    <th>زمان به ساعت</th>
                </tr>
                </thead>

                <tbody>

                <?php foreach ($jobTimes as $r) {
                    ?>
                    <tr>
                        <td><?php echo $r->title; ?></td>
                        <td><?php echo $r->time; ?></td>
                        <td><?php echo ceil($r->time / 60); ?></td>
                        <td><?php echo floor($r->time / 3600); ?></td>
                    </tr>
                    <?php
                } ?>

                </tbody>
            </table>

        </div>

    </div>
    <?php
}
