# AutoAssemblyAutomation
Task for job applicatioon
PHP 8 !
This project is aimed to create Order Automobile Automation.
Run it by 
php .\run.php             '[{              
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
            }]'

