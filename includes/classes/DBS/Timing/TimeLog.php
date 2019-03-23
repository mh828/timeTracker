<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 2/7/2019
 * Time: 07:45
 */

namespace DBS\Timing;


class TimeLog extends \BaseTable
{
    public $rowid = 1;
    public $pk_start;
    public $pk_job_id;

    public $start;
    public $start_date;
    public $end;
    public $end_date;
    public $job_id;
    public $duration;
    public $description;

    public function validation()
    {
        $this->errors = array();
        if (empty($this->start))
            $this->errors['start'] = 'زمان شروع تعیین نشده است';
        if (empty($this->end))
            $this->errors['end'] = 'زمان پایان تعیین نشده است';
        else if ($this->end <= $this->start)
            $this->errors['end'] = 'زمان پایان کمتر از شروع است';
        if (empty($this->job_id))
            $this->errors['job_id'] = 'فعالیت تعیین نشده است';
    }

    public function save()
    {
        if ($this->is_valid(true)) {
            $stm = null;
            $insert = empty($this->pk_start);

            $this->duration = calculate_time($this->start, $this->end);

            if ($insert) {
                $stm = $this->pdo->prepare("INSERT INTO time_log ( start, [end], job_id, duration, description ) " .
                    " VALUES ( :start, :end, :job_id, :duration, :description );");
            } else {
                $stm = $this->pdo->prepare("UPDATE time_log SET " .
                    " start = :start, [end] = :end, job_id = :job_id, " .
                    " duration = :duration, description = :description " .
                    " WHERE [start] = :pk_start AND [job_id] = :pk_job_id ");

                $stm->bindValue(":pk_start", $this->pk_start);
                $stm->bindValue(":pk_job_id", $this->pk_job_id);
            }

            $stm->bindValue(":start", $this->start);
            $stm->bindValue(":end", $this->end);
            $stm->bindValue(":job_id", $this->job_id);
            $stm->bindValue(":duration", $this->duration);
            $stm->bindValue(":description", $this->description);

            if ($res = $stm->execute()) {
                if ($insert)
                    $this->rowid = $this->pdo->lastInsertId();
            }

            return $res ? $this->rowid : false;
        }
    }

    public function load_by_primary_keys($start, $job_id)
    {
        $stm = $this->pdo->prepare("SELECT * FROM [time_log] WHERE [start] = :start AND [job_id] = :job_id");
        $stm->bindValue(":start", $start);
        $stm->bindValue(":job_id", $job_id);
        $stm->execute();

        $this->fillByStd($stm->fetchObject(), false);
        $this->start_date = !empty($this->start) ? jdate("Y/m/d H:i:s", $this->start, '', '', 'en') : '';
        $this->end_date = !empty($this->end) ? jdate("Y/m/d H:i:s", $this->end, '', '', 'en') : '';
        $this->pk_start = $this->start;
        $this->pk_job_id = $this->job_id;
    }
}