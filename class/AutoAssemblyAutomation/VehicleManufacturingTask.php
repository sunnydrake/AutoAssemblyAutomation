<?php

namespace AutoAssemblyAutomation;

/**
 * Class that describe manufacturing task
 */
class VehicleManufacturingTask
{
    /**
     * @var string Type of Vehicle "Truck" or "Passenger"
     */
    public string $type='';
    /**
     * @var DataMarshaling\GenericDataToStrBase[] of instance AutomobileParts\(Engine,VehicleBody,Transmission,Interior,Color,VehicleOptions) that extends AutomobileParts\GenericDataToStrBase
     */
    public array $config=[];
    /**
     * @var string Name of Task
     */
    public string $name='';
    /**
     * @var int Serial Number of Task
     */
    public int $serial=0;
    /**
     * Flag that reports if task is done
     * @var bool
     */
    public bool $readyFlag=false;

    /**
     * constructor
     */
    function __construct()
    {
        $this->readyFlag = false;

    }
}

