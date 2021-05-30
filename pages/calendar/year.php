<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 3/23/2019
 * Time: 10:40
 */

global $TITLE;
$TITLE = "تقویم - نمایش سالانه";

Statics::addBundle(Statics::BUNDLE_JDF);

function body()
{
    $currentYear = $year = jdate("Y", '', '', '', 'en');
    $dontShowPassed = $_GET['dont_show_passed'] ?? false;

    if (!empty(URL_PARAMS[0]) && preg_match("/^[0-9]+$/", URL_PARAMS[0])) {
        $year = URL_PARAMS[0];
    }

    $jd = new \Utility\Assistant\JalaliDate();
    $yearTime = jmktime(0, 0, 0, 1, 1, $year);
    ?>

    <div class="container">

        <?php if ($currentYear == $year): ?>
            <div class="my-2">
                <b>تعداد روز (کامل) گذشته از سال :</b>
                <span><?php echo jdate("z", '', '', '', 'en') ?></span>

                <span class="mx-1">|</span>

                <b>در صد گذشته از سال :</b>
                <span><?php echo jdate("K", '', '', '', 'en') ?></span>

                <span class="mx-1">|</span>

                <b>تعداد روز (کامل) باقی مانده از سال :</b>
                <span><?php echo jdate("Q", '', '', '', 'en') ?></span>

                <span class="mx-1">|</span>

                <b>در صد باقیمانده از سال :</b>
                <span><?php echo jdate("k", '', '', '', 'en') ?></span>

            </div>
        <?php endif; ?>

        <form class="my-2">
            <div class="form-check">
                <input type="checkbox" class="form-check-input"
                    <?= $dontShowPassed ? 'checked' : '' ?>
                       id="show_passed_checkbox" value="1" name="dont_show_passed"/>
                <label class="form-check-label" for="show_passed_checkbox">
                    نمایش زمان گذشته
                </label>

                <input type="submit" class="btn btn-primary" value="فیلتر"/>
            </div>
        </form>


        <div class="table-responsive">
            <table class="table table-bordered">

                <thead class="thead-dark">
                <tr class="text-center">
                    <th>روز / ماه</th>

                    <th>فروردین</th>
                    <th>اردیبهشت</th>
                    <th>خرداد</th>
                    <th>تیر</th>
                    <th>مرداد</th>
                    <th>شهریور</th>
                    <th>مهر</th>
                    <th>آبان</th>
                    <th>آذر</th>
                    <th>دی</th>
                    <th>بهمن</th>
                    <th>اسفند</th>
                </tr>
                </thead>

                <tbody>
                <?php
                $months = array();
                $first_days = array();
                $last_days = array();
                for ($i = 1; $i <= 12; $i++) {
                    $months[$i] = 0;
                    $first_days[$i] = jdate("w", jmktime(0, 0, 0, $i, 1, $year), '', '', 'en');
                    $last_days[$i] = jdate("t", jmktime(0, 0, 0, $i, 2, $year), '', '', 'en');
                }

                while (count($months) > 0) {
                    for ($d = 0; $d <= 6 && count($months) > 0; $d++) {
                        $day = $jd->days_name($d);

                        $classes = "";
                        if ($d == 6)
                            $classes .= "bg-light";

                        echo "<tr> <td class='bg-dark text-white'>{$day}</td>";
                        for ($m = 1; $m <= 12; $m++) {
                            if (!isset($months[$m])) {
                                echo "<td class='{$classes}'></td>";
                                continue;
                            }

                            if ($months[$m] == 0 && $first_days[$m] == $d) {
                                $months[$m] = 1;
                            }


                            if ($months[$m] > 0) {
                                $tmp_class = ' text-center ';


                                if (!$dontShowPassed && jmktime(0, 0, 0, $m, $months[$m], $year) <= time())
                                    $tmp_class .= " outline ";

                                if ($d == 6)
                                    $tmp_class .= " bg-light ";

                                echo "<td class='{$tmp_class}'>{$months[$m]}</td>";

                                //increase day of months
                                $months[$m] += 1;
                                //if months greater than total days of months
                                if ($months[$m] > $last_days[$m]) {
                                    unset($months[$m]);
                                }

                            } else {
                                echo "<td class='{$classes}'></td>";
                            }

                        }

                        echo "</tr>";
                    }
                }
                ?>
                </tbody>

            </table>
        </div>

    </div>

    <?php
}