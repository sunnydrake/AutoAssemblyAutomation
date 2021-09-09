<?php

namespace AutoAssemblyAutomation\DataMarshaling;

use AutoAssemblyAutomation\DataTypes\DataVault;

/**
 * Class to be inherited by AutomobilePartsClasses
 */
class GenericDataToStrBase
{
    /**
     * @var DataVault $data Generic data vault
     */
    public DataVault $data;

    /**
     * Generic class constructor that takes data for initialization
     * @param $data
     */
    public function __construct($data){
        $this->data=new DataVault();
        foreach ($data as $key=>$value) $this->data->setData($key,$value);
    }
    /**
     * Common Class for interop that return string representation of data
     * @return string
     */
    public function __toString():string
    {
        return $this->data;
    }
}