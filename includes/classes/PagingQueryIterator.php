<?php
/**
 * Created by PhpStorm.
 * User: Mahdi Hasanpour
 * Date: 11/26/2018
 * Time: 23:28
 */

abstract class PagingQueryIterator extends PagingQuery implements Iterator
{
    /**
     * @var PDO
     */
    protected $pdo;
    protected $errors;

    protected $index = 0;

    public function __construct($showInPage = 10, $page = 0)
    {
        parent::__construct($showInPage, $page);
        $this->pdo = Statics::get_pdo();
    }

    public abstract function do_query();

    public function current()
    {
        return $this->rows[$this->index];
    }

    public function next()
    {
        $this->index += 1;
    }

    public function key()
    {
        return $this->index;
    }

    public function valid()
    {
        return isset($this->rows[$this->index]);
    }

    public function rewind()
    {
        $this->index = 0;
    }

}