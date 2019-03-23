<?php
/**
 * Created by PhpStorm.
 * User: Mahdi Hasanpour
 * Date: 11/26/2018
 * Time: 23:28
 */

abstract class BaseView extends PagingQuery implements Iterator
{
    /**
     * @var PDO
     */
    protected $pdo;
    protected $errors;

    private $_conditions = array();
    private $_conditions_params = array();
    private $_orders = array();

    protected $index = 0;

    public function __construct($showInPage = 10, $page = 0)
    {
        parent::__construct($showInPage, $page);
        $this->pdo = get_pdo();
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

    /**
     * @param PDOStatement $queryStatement
     * @return PDOStatement
     */
    protected function bindParams($queryStatement)
    {
        $props = get_object_vars($this);
        $res = array();
        preg_match_all("/\:\w+/", $queryStatement->queryString, $res);
        foreach ($res[0] as $item) {
            $propName = ltrim($item, ':');
            $queryStatement->bindValue(
                $item,
                isset($props[$propName]) ? $props[$propName] : ''
            );
        }
        return $queryStatement;
    }


    protected function add_conditions($key, $condition, $param)
    {
        $this->_conditions[$key] = $condition;
        $this->_conditions_params[$key] = $param;
    }

    protected function get_conditions()
    {
        return count($this->_conditions) > 0 ? " WHERE " . implode(" AND ", array_values($this->_conditions)) : "";
    }

    protected function add_order($key, $order)
    {
        $this->_orders[$key] = $order;
    }

    protected function get_orders()
    {
        return count($this->_orders) > 0 ? " ORDER BY  " . implode(", ", array_values($this->_orders)) : "";
    }

    /**
     * @param PDOStatement $queryStatement
     * @param $params
     * @return PDOStatement
     */
    protected function bindParamsWithArray($queryStatement, $params = null)
    {
        if ($params === null)
            $params = $this->_conditions_params;
        $res = array();
        preg_match_all("/\:\w+/", $queryStatement->queryString, $res);
        foreach ($res[0] as $item) {
            $queryStatement->bindValue(
                $item,
                isset($params[$item]) ? $params[$item] : null
            );
        }
        return $queryStatement;
    }

    /**
     * @param $is_count
     * @param $table_name
     * @param string $fields
     * @return bool|\PDOStatement
     */
    protected function query_generator($is_count, $table_name, $fields = "*")
    {
        $select_field = $is_count ? " COUNT({$fields}) " : " {$fields} ";
        $conditions = $this->get_conditions();
        $orders = $is_count ? '' : $this->get_orders();
        $query = "SELECT {$select_field} FROM [{$table_name}] {$conditions} {$orders} ";
        $query = $this->pdo->prepare($is_count ? $query : $this->appendLimit($query));
        $this->bindParams($query);

        $query->execute();
        return $query;
    }

}