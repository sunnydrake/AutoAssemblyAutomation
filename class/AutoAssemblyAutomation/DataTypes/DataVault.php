<?php

namespace AutoAssemblyAutomation\DataTypes;

/**
 * Class that hold and process data in form of associative array
 */
class DataVault
{
    /** @var array Associative array data storage */
    public array $data = [];

    /**
     * get Data
     * @param string|null $key Data Key
     * @return mixed
     */
    public function getData(string $key = null): mixed
    {
        if (empty($key)) return $this->data;
        return $this->data[$key];
    }

    /**
     * set Data
     * @param string|null $key Param Name
     * @param mixed $data Data
     * @return bool State of operation
     */
    public function setData(string $key = null, mixed $data = null): bool
    {
        if (empty($key)) return false;
        if (is_array($data)) {
            foreach ($data as $name => $value)
                $this->data[$name] = $value;
        } else {
            $this->data[$key] = $data;
        }
        return true;
    }

    /**
     * @return string Return String representation of DataVault Values
     */
    public function __toString()
    {
        return implode(",", $this->data);
    }
}