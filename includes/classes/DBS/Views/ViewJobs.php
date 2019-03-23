<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 3/23/2019
 * Time: 07:40
 */

namespace DBS\Views;


class ViewJobs extends \BaseView
{

    public function do_query()
    {
        $query = $this->query_generator(true, "job", '[job_id]');
        $this->totalRows = $query->fetchColumn();
        $this->pageCountCalculate();

        $query = $this->query_generator(false, "job");
        while ($r = $query->fetchObject()) {
            $this->rows [] = $r;
        }
    }
}