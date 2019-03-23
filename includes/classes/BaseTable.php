<?php
/**
 * Created by PhpStorm.
 * User: Mahdi Hasanpour
 * Date: 11/26/2018
 * Time: 23:28
 */

abstract class BaseTable
{
    /**
     * @var PDO
     */
    protected $pdo;
    protected $errors;

    public function __construct()
    {
        $this->pdo = get_pdo();
    }

    public function getErrors($force_validate = false)
    {
        if (empty($this->errors) || $force_validate)
            $this->validation();
        return $this->errors;
    }

    public abstract function validation();

    public function is_valid($force_validation)
    {
        return count($this->getErrors($force_validation)) == 0;
    }

    /**
     * @param $data
     * @param bool $validate_data
     * @param null|array $include array of fields
     */
    public function fillByStd($data, $validate_data = true, $include = null)
    {
        if ($validate_data)
            $data = $this->input_validate($data);

        $props = new ReflectionObject($this);
        $props = $props->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($props as $prop) {
            $name = $prop->name;
            if ($include === null || in_array($name, $include))
                $this->$name = isset($data->$name) ? $data->$name : $this->$name;
        }
    }

    public abstract function save();

    function input_validate($input)
    {
        if (is_array($input) || is_object($input)) {
            foreach ($input as $k => $v) {
                if (is_array($input))
                    $input[$k] = $this->input_validate($v);
                else
                    $input->$k = $this->input_validate($v);
            }
        } else {
            $input = trim($input);
            $input = stripslashes($input);
            $input = htmlspecialchars($input);
        }

        return $input;
    }
}