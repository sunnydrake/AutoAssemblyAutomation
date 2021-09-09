<?php

namespace AutoAssemblyAutomation;

/**
 *
 */
class CommandLineConfigurator
{
    /**
     * Function that process json string to class instances and run ops on it
     * @param mixed $arg argv argument of command line params
     */
    public static function process(mixed $arg)
    {
        unset($arg[0]);
        $arg=trim(implode($arg),"'\"");

        if (empty($arg)) {
            print(' Please Supply Json String as param. Example:  
            \'[{
                \"Order\":
                 [ 
                  {\"logTo\":[\"fileWrite\"]},    
                  {\"debugRun\": 1},                     
                  {\"assembly\":
                    [ 
                    {\"name\": \"Generic Car Hardware #777\"},
                    {\"type\": \"motor\"},
                    {\"Engine\":{\"type\":\"diesel\",\"volume\":\"10L\"}},
                    {\"VehicleBody\":{\"type\":\"sport\"}},
                    {\"Transmission\":{\"type\":\"manual\",\"speeds\":\"5+1\"}}                
                    ]
                   },           
                   {\"assembly\":[  
                    {\"name\": \"Generic Car Style #777\"},
                    {\"type\": \"motor\"},
                    {\"Interior\":{\"type\":\"deluxe\"}},
                    {\"Interior\":{\"type\":\"common\"}},
                    {\"Color\":{\"type\":[\"pink\",\"green\"]}},
                    {\"VehicleOptions\":{\"serial\":\"Yo!Man\"}} 
                    ]   
                   },     
                   
                   {\"clientName\":\"Justin\"},           
                   {\"managerName\":\"Capitan Jack\"},
                   {\"clientMail\":\"admin@localhost\"},
             
     
                   {\"processingAction\":\"Assembly[Generic Car Hardware #777] of Engine\"},
                   {\"finishAction\":\"Assembly[Generic Car Hardware #777] of Engine\"},              
                   {\"finishTask\":\"Generic Car Hardware #777\"},
                   
                   {\"printCharacteristics\":\"\"}              
                 ]
            }]\'');
            return;
        }


        $params = json_decode($arg);

        if ( $params == null) {
            print("\n Can't decode JSON! ".json_last_error_msg()."\n Param : \n".$arg);
            return;
        }
        if (empty($params)) {
            print("\n No data!");
            return;
        }
        if (!is_array($params)) {
            print("\n No data! Provide a valid json string. To see example run without params.");
            return;
        }
        $orderList = [];
        foreach ($params as $aParam)
            foreach ($aParam as $key => $value) {
            switch ($key) {
                case 'Order':
                    $order = new Order();
                    foreach ($value as $val)
                    foreach ($val as $op => $param) {
                        if ($op == 'printCharacteristics') {
                            print($order->printCharacteristics());
                        } elseif (is_array($param)) {
                            if (method_exists($order, $op)) {
                                $order->$op($param);
                            } elseif (property_exists($order, $op)) {
                                $order->$op = $param;
                            }
                        } elseif (method_exists($order, $op)) {
                            $order->$op($param);
                        } elseif (property_exists($order, $op)) {
                            $order->$op=$param;
                        } else {
                            print('\n Order - Unexpected operation - op:' . $op . " param: - " . $param);
                        }
                    }
                    $orderList[] = $order;
                    break;

                case 'List Orders':
                    print('\n List orders:');
                    foreach ($orderList as $order) {
                        print('\n  ' . $order);
                    }
                    break;
                default:
                    print('\n Unexpected param :' . $key);
            }
        }
    }
}

