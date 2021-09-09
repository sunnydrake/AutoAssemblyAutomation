<?php

namespace AutoAssemblyAutomation\DataTypes;

/**
 *  Allowed ActionItem
 */
class ActionAllowed
{
    /**
     * @var ActionItem $actionAllowed instance of ActionItem
     */
    public ActionItem $actionAllowed;
    /**
     * @var string $itemsRef Key name of Order->items->serial
     */
    public string $itemsRef;

    /**
     *  Use Only this to get instance of class
     * @param string $name Name of ActionItem
     * @param string|null $orderItemsKey (optional) associated Key of Order->items->serial key
     * @return ActionAllowed|false instance of ActionAllowed or false on fail
     */
    public static function instance(string $name, string $orderItemsKey = null): ActionAllowed|false
    {
        if (empty($name)) return false;
        $instance = new ActionAllowed();
        $instance->actionAllowed = ActionItem::instance($name);
        if ($instance->actionAllowed === false) return false;
        $instance->itemsRef = $orderItemsKey;
        return $instance;
    }
    public function __get(string $name)
    {
        if ($name=="name") return $this->actionAllowed->name;
        if (isset($this->$name)) return $this->$name;
        return null;
    }
}