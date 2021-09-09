<?php

namespace AutoAssemblyAutomation\DataTypes;

/**
 * Declares Generic NamedAction
 */
class ActionItem
{
    /**
     * @var string $name Name of ActionItem
     */
    public string $name;

    /**
     *  Use Only this to get instance of class
     * @param string $name Name of ActionItem
     * @return ActionItem|false instance of ActionItem or false on fail
     */
    public static function instance(string $name = ''): ActionItem|false
    {
        if (empty($name)) return false;
        $instance = new ActionItem();
        $instance->name = $name;
        return $instance;
    }
}