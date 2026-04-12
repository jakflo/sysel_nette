<?php
require __DIR__ . '/../vendor/autoload.php';

use \App\BootstrapType;

function createContainer(): Nette\DI\Container
{
    // musi byt BootstrapType::test|BootstrapType::ciTest, jinak tester smaze data z ostre DB
    if (getenv('ciTest')) {
        $bootstrap = new \App\Bootstrap(BootstrapType::ciTest);
    } else {
        $bootstrap = new \App\Bootstrap(BootstrapType::test);
    }
    
    $container = $bootstrap->bootWebApplication();
    return $container;
}
