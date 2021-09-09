<?php
spl_autoload_register(function ($class_name) {
    include "./class/".$class_name . '.php';
});
use AutoAssemblyAutomation\CommandLineConfigurator;

CommandLineConfigurator::process($argv);
