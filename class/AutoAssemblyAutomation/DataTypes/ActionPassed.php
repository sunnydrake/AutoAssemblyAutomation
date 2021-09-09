<?php

namespace AutoAssemblyAutomation\DataTypes;

use DateTime;

/**
 * ActionItem that passed
 */
class ActionPassed
{
    /**
     * @var DateTime $end TimeStamp of this operation
     */
    public DateTime $end;
    /**
     * @var ActionInProcess $actionPassed ActionInProcess instance;
     */
    public ActionInProcess $actionPassed;

    /**
     * Use Only this to get instance of class
     * @param ActionInProcess &$ActionInProcess ref to ActionInProcess instance
     * @return ActionPassed|false instance of ActionPassed or false on fail
     */
    public static function instance(ActionInProcess &$ActionInProcess): ActionPassed|false
    {
        if (empty($ActionInProcess)) return false;
        $instance = new ActionPassed();
        $instance->end = new DateTime();
        $instance->actionPassed = $ActionInProcess;
        return $instance;
    }

    public function __get(string $name)
    {
        if ($name=="name") return $this->actionPassed->actionInProcess->actionAllowed->name;
        if (isset($this->$name)) return $this->$name;
        return null;
    }
}