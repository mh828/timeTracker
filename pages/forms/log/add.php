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
    $model = new \DBS\Timing\TimeLog();

    $jobs = new \DBS\Views\ViewJobs(0);
    $jobs->do_query();
    $errors = array();

    if (!empty($_POST)) {
        $model->fillByStd((object)$_POST);
        $model->start = \Utility\Assistant\JalaliDate::convertStringToTime($model->start_date);
        $model->end = \Utility\Assistant\JalaliDate::convertStringToTime($model->end_date);

        $model->setValidateEndField(false);
        if ($model->save()) {
            header("location:" . BASE_URL . '/list2');
            exit();
        } else {
            $errors = $model->getErrors();
        }
    } else if (!empty($_GET['start']) && !empty($_GET['job_id'])) {
        $model->load_by_primary_keys($_GET['start'], $_GET['job_id']);
    }
    ?>
    <div class="container">
        <form method="post" class="card">

            <input type="hidden" name="pk_job_id" value="<?php echo $model->pk_job_id ?>"/>
            <input type="hidden" name="pk_start" value="<?php echo $model->pk_start ?>"/>

            <h4 class="card-header">ثبت زمان</h4>

            <div class="card-body">

                <div class="form-row">

                    <div class="form-group col-md-6">
                        <label>شروع</label>

                        <input type="text"
                               placeholder="YYYY/mm/dd HH:ii[:ss]"
                               name="start_date" value="<?php echo $model->start_date ?>" class="form-control"/>
                    </div>

                    <div class="form-group col-md-6">
                        <label>پایان</label>

                        <input type="text"
                               placeholder="YYYY/mm/dd HH:ii[:ss]"
                               name="end_date" value="<?php echo $model->end_date ?>" class="form-control"/>
                    </div>

                </div>

                <div class="form-group">
                    <label>فعالیت</label>
                    <select class="form-control" name="job_id">
                        <option value=""> -- انتخاب فعالیت --</option>

                        <?php foreach ($jobs as $job) : ?>
                            <option value="<?php echo $job->job_id ?>" <?php echo ($model->job_id == $job->job_id) ? 'selected="selected"' : '' ?> >
                                <?php echo $job->title; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>توضیحات</label>
                    <textarea name="description" class="form-control"><?php echo $model->description; ?></textarea>
                </div>

                <ul class="text-danger">
                    <?php foreach ($errors as $error) {
                        echo "<li>{$error}</li>";
                    } ?>
                </ul>

            </div>

            <div class="card-footer">
                <input type="submit" value="ثبت" class="btn btn-success"/>
            </div>
        </form>
    </div>


    <style>
        input[type=number] {
            width: 50px;
        }
    </style>
    <?php
}


