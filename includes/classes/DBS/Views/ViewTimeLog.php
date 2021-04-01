<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 1/3/2019
 * Time: 22:14
 */

namespace DBS\Views;

use DBS\Views\Model\ModelTimeLog;

/**
 * Class ViewTimeLog
 * @package DBS\Views
 * @method ModelTimeLog current()
 */
class ViewTimeLog extends \BaseView
{
    private $filter_job_id;
    private $filter_undone_job = true;

    /**
     * @param $job_id
     * @return $this
     */
    public function filter_job_id($job_id)
    {
        $this->filter_job_id = $job_id;
        return $this;
    }

    public function hide_undone(bool $value)
    {
        $this->filter_undone_job = $value;
        return $this;
    }

    /**
     * @return $this
     */
    public function do_query()
    {
        $query = "SELECT COUNT([start]) FROM [time_log] WHERE 1 ";
        if (!empty($this->filter_job_id))
            $query .= " AND [job_id] =  '{$this->filter_job_id}' ";

        $query = $this->pdo->query($query);
        $this->totalRows = $query->fetchColumn();
        $this->pageCountCalculate();

        $query = "SELECT [time_log].[rowid], [time_log].*,[job].[title] as [job_title] FROM [time_log] " .
            " LEFT JOIN [job] ON [job].[job_id] = [time_log].[job_id] " .
            " WHERE 1 ";
        if ($this->filter_undone_job)
            $query .= ' AND [end] IS NOT NULL ';
        if (!empty($this->filter_job_id))
            $query .= " AND [time_log].[job_id] =  '{$this->filter_job_id}' ";

        $query .= "ORDER BY [start] DESC";
        $query = $this->pdo->query($this->appendLimit($query));

        while ($r = $query->fetchObject()) {
            $this->rows[] = $r;
        }

        return $this;
    }
}