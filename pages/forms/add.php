<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 1/4/2019
 * Time: 00:37
 */

global $TITLE;
$TITLE = 'ثبت زمان کاری';
Statics::addBundle(Statics::BUNDLE_JDF);
function body()
{
    if(!empty($_POST['start'])){
        echo Statics::convert_jalali_to_time($_POST['start']);
    }

    ?>
    <div class="container">
        <form method="post" class="card">
            <h4 class="card-header">ثبت زمان</h4>

            <div class="card-body">

                <div class="form-group">
                    <label>از</label>
                    <input type="text" name="start" value="" class="form-control" />
                </div>

            </div>

            <div class="card-footer">
                <input type="submit" value="ثبت" class="btn btn-success" />
            </div>
        </form>
    </div>


    <style>
        input[type=number]{
            width: 50px;
        }
    </style>
    <?php
}

