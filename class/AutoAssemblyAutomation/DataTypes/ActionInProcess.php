<?php

namespace AutoAssemblyAutomation\DataTypes;

use DateTime;

/**
 * ActionItem that in process
 */
class ActionInProcess
{
    /**
     * @var DateTime $start TimeStamp of this operation creation
     */
    public DateTime $start;
    /**
     * @var ActionAllowed $actionInProcess ActionAllowed instance
     */
    public ActionAllowed $actionInProcess;

    /**
     * Use Only this to get instance of class
     * @param ActionAllowed &$actionAllowed ref to ActionAllowed instance
     * @return ActionAllowed|false instance of ActionAllowed or false on fail
     */
    public static function instance(ActionAllowed &$actionAllowed): ActionInProcess|false
    {
        if (empty($actionAllowed)) return false;
        $instance = new ActionInProcess();
        $instance->start = new DateTime();
        $instance->actionInProcess = $actionAllowed;
        return $instance;
    }
    public function __get(string $name)
    {
        if ($name=="name") return $this->actionInProcess->actionAllowed->name;
        if (isset($this->$name)) return $this->$name;
        return null;
    }
}