<?php


namespace DBS\Views;


/**
 * Class ViewJobTimes
 * @package DBS\Views
 */
class ViewJobTimes extends \BaseView
{

    public function do_query()
    {
        $this->add_conditions('time', 'time > 0', null);

        $query = $this->query_generator(true, "view_job_times", '[job_id]');
        $this->totalRows = $query->fetchColumn();
        $this->pageCountCalculate();


        $query = $this->query_generator(false, "view_job_times");
        while ($r = $query->fetchObject()) {
            $this->rows [] = $r;
        }
    }
}