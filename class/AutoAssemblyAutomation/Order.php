<?php

namespace AutoAssemblyAutomation;
use AutoAssemblyAutomation\AutomobileParts;
use AutoAssemblyAutomation\DataTypes\ActionAllowed;
use AutoAssemblyAutomation\DataTypes\ActionInProcess;
use AutoAssemblyAutomation\DataTypes\ActionPassed;
use AutoAssemblyAutomation\Log\Logger;
use AutoAssemblyAutomation\Notify\ClientMail;
use DateTime;

/**
 * Class to Make Orders
 */
class Order
{
    /**
     * @var VehicleManufacturingTask[] $items Named(arrayKey) Ordered Tasks of VehicleManufacturingTask instances
     */
    public array $items=[];

    /**
     * @var string Client Name
     */
    public string $clientName;
    /**
     * @var string Manager Name
     */
    public string $managerName;
    /**
     * @var DataTypes\ActionAllowed[] reducing array of DataTypes\ActionAllowed
     *
     */
    public array $allowedActions=[];
    /**
     * @var DataTypes\ActionInProcess[]  array of DataTypes\ActionInProcess
     */
    public array $processingActions=[];
    /**
     * @var DataTypes\ActionPassed[] array of DataTypes\ActionPassed
     */
    public array $passedActions=[];
    /**
     * @var Log\Logger $log instance of Logger
     */
    public Log\Logger $log;

    /**
     * @var ClientMail
     */
    public ClientMail $clientNotify;

//methods
    /**
     * Initializer
     */
    public function __construct()
    {
        $this->log = new Logger();
        $this->clientNotify = new ClientMail();
    }
// functions intended to call from configurator or other places
    /**
     * Action That finishes specific VehicleManufacturingTask by name
     * @param string $taskName Task name from order items list
     * @return false|void
     */
    public function finishTask(string $taskName)
    {
        if (empty($this->items[$taskName])) return false;

        $this->items[$taskName]->readyFlag = true;
        $this->log->message("Finish Task " . $taskName);
        if ($this->checkIfAssemblyFinished()) {
            $this->printCharacteristics();
            $this->clientNotify->message("Finish build");
        }
    }

    /**
     * checks if assembly of parts finished
     * @return bool
     */
    public function checkIfAssemblyFinished(): bool
    {
        foreach ($this->items as $item)
            if ($item->readyFlag === false) return FALSE;
        return true;
    }

    /**
     * return as c-string current state of order and parts
     * @return string
     */
    public function printCharacteristics(): string
    {
        $buff = "\n:: printCharacteristics ::";
        $buff .= "\n \$ Order  ";
        $buff .= "\n # Manager  $this->managerName";
        $buff .= "\n # Client   $this->clientName";


        $buff .= "\n > VehicleManufacturingTask's  >";
        foreach ($this->items as $key => $value) {
            $buff .= "\n A Item name: " . $key;
            $buff .= "\n a Item type: " . $value->type;
            $buff .= "\n a Subtasks ready status: " . (($value->readyFlag)?"Yes":"No");
            $buff .= "\n o Items o:";
            foreach ($value->config as $item) {
                $buff .= "\n i " . basename($item::class)." > ".$item;
            }
        }
            $buff .= "\n x Finished actions x:";
            foreach ($this->passedActions as $action)
                $buff .= "\n   " . $action->end->format(DateTime::W3C) . " " . $action->actionPassed->actionInProcess->actionAllowed->name .
                    ", Assembly Serial Key " . $action->actionPassed->actionInProcess->itemsRef;
            $buff .= "\n r Actions in progress r:";
            foreach ($this->processingActions as $action)
                $buff .= "\n   " . $action->start->format(DateTime::W3C) . " " . $action->actionInProcess->actionAllowed->name .
                    ", Assembly Serial Key " . $action->actionInProcess->itemsRef;
            $buff .= "\n 0 Actions left to process 0:";
            foreach ($this->allowedActions as $action)
                $buff .= "\n   " . $action->actionAllowed->name .
                    ", Assembly Serial Key " . $action->itemsRef;

        return $buff;
    }

    /**
     * Mark processing of order allowedAction
     * @param string $actionName Action Name from allowedAction list or item Name (first will be fetched)
     * @return void
     */
    public function processingAction(string $actionName)
    {
        $match=$this->findByAttribute($this->allowedActions,"name",[$actionName]);
        if (!isset($match[0])) {
            $value = strstr($actionName, "[");
            if ($value===false) return;
            $value=ltrim($value,'[');
            $value = strstr($value, "]", true);
            if ($value===false) return;
            $serials=[];
            foreach ( $this->findByAttribute($this->items,"name",[$value]) as $obj) {
                $serials[]=$obj[1]->serial;
            };
            if (count($serials)==0) return;
            $query=[];
            foreach ($serials as $serial) {
                $query[]=preg_replace("/$value/i", $serial, $actionName,1);
            }
            $match=$this->findByAttribute($this->allowedActions,"name",$query);
            if (!isset($match[0])) return;

        }
        $actionName=$match[0];
        $this->processingActions[] = ActionInProcess::instance($actionName[1]);
        unset($this->allowedActions[$actionName[0]]);
        $this->log->message("Processing Action " . $actionName[1]->name);
    }

    /**
     * Finishes order action
     * @param string $actionName Action name from processingAction list or item Name (first will be fetched)
     * @return void
     */
    public function finishAction(string $actionName)
    {
        $match=$this->findByAttribute($this->processingActions,"name",[$actionName]);
        if (!isset($match[0])) {
            $value = strstr($actionName, "[");
            if ($value===false) return;
            $value=ltrim($value,'[');
            $value = strstr($value, "]", true);
            if ($value===false) return;
            $serials=[];
            foreach ( $this->findByAttribute($this->items,"name",[$value]) as $obj) {
                $serials[]=$obj[1]->serial;
            };
            if (count($serials)==0) return;
            $query=[];
            foreach ($serials as $serial) {
                $query[]=preg_replace("/$value/i", $serial, $actionName,1);
            }
            $match=$this->findByAttribute($this->processingActions,"name",$query);
            if (!isset($match[0])) return;

        }
        $actionName=$match[0];
        $this->passedActions[] = ActionPassed::instance($actionName[1]);
        unset($this->processingActions[$actionName[0]]);
        $this->log->message("Finish Action " . $actionName[1]->name);
        $serial=$actionName[1]->actionInProcess->itemsRef;
        foreach($this->processingActions as $PA) if ($PA->actionInProcess->itemsRef==$serial) return;
        foreach($this->allowedActions as $AA) if ($AA->itemsRef==$serial) return;
        foreach($this->items as $key=>$item) if ($item->serial==$serial) $this->finishTask($key);
    }

    /**
     * Main function that creates assembly and support action tasks
     * @param mixed $param Array of .. of params
     */
    public function assembly(mixed $param)
    {
        if (empty($param) or !(is_object($param) or is_array($param))) return;
        $assembly = new VehicleManufacturingTask();
        $assembly->serial=hrtime(true);
        $this->assemblyRecursion($assembly,$param);
        if (empty($assembly->name)) {
            $assembly->name = hrtime(true);
        }
        if (array_key_exists($assembly->name,$this->items)) {
            $newName = $assembly->name.hrtime(true);
            $this->log->message("Assembly[$assembly->serial]: Duplicate name replace $assembly->name with $newName");
            $assembly->name=$newName;
        }
        $this->items[$assembly->name]=$assembly;
    }

    /**
     * Internal AllowedAction&config Creating function
     * @param VehicleManufacturingTask $assembly linkto assembly
     * @param mixed $param Parameters to process
     * @param string $key Processing object name
     * @param string $parent Parent object name
     * @return void
     */
    private function assemblyRecursion(VehicleManufacturingTask &$assembly, mixed $param, string $key='assembly',string $parent='assembly'):void {
        if (empty($param)) return;
        if ((is_array($param) or is_object($param)) and !@class_exists("AutoAssemblyAutomation\\AutomobileParts\\$key"))
        {
            foreach ($param as $sKey=>$value) {

                    $this->assemblyRecursion($assembly,$value,(empty($sKey) or is_array($param))?$key:$sKey,$key);
            }
        } else {
            switch ($key) {
                case 'type':
                case 'name':
                    if ($param=='0') die();
                    if ($parent=='assembly') {$assembly->$key=$param;break;}
                default:
                    if (@class_exists("AutoAssemblyAutomation\\AutomobileParts\\$key")) {
                        $item = new ('\AutoAssemblyAutomation\AutomobileParts\\'.$key)($param);
                        $assembly->config[] = $item;
                        $this->allowedActions[] = ActionAllowed::instance("Assembly[$assembly->serial] of $key", $assembly->serial);
                        break;
                    }
                    $this->log->message(" Assembly[$assembly->serial]: unknown command $key value $param");
            }
        }
    }
    /**
     * set Log storage
     * @param array $param array of optional values ["fileWrite","mailWrite"]
     */
    public function logTo(array $param)
    {
        $this->log = new Logger($param);
    }

    /**
     * setClient Mail
     * @param string $param email
     */
    public function clientMail(string $param)
    {
        if (!empty($param)) $this->clientNotify->clientMail = $param;
    }

    /**
     * Set Debug run and suppress email client messaging
     * @param mixed $param
     */
    public function debugRun(mixed $param){
        $this->clientNotify->setConsoleOnly($param);
    }


    /**
     * find Attribute on object|array and return matched objects
     * @param mixed $obj Heap
     * @param string $attr needle
     * @param array $match array of matches to check
     * @return array
     */
    private function findByAttribute(mixed &$obj, string $attr, array $match):array
    {
        $ret=[];
        if (is_array($obj)) {
            foreach($obj as $ko=>$o){
                if (isset($o->$attr) or ($o->$attr!=null)) {
                    if (in_array($o->$attr,$match) ) $ret[]=[$ko,$o];
                }
            }
        } else {
            if (isset($obj->$attr)) return [[null,$obj]];
        }
        return $ret;
    }
}
