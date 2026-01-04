<?php
require __DIR__ . '/../vendor/autoload.php';

use \App\BootstrapType;

function createContainer(): Nette\DI\Container
{
    $bootstrap = new \App\Bootstrap(BootstrapType::test);// musi byt BootstrapType::test, jinak tester smaze data z ostre DB
    $container = $bootstrap->bootWebApplication();
    return $container;
}
