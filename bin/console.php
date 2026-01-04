<?php

require __DIR__ . '/../vendor/autoload.php';

use \App\BootstrapType;

$bootstrap = new \App\Bootstrap(BootstrapType::console);
exit($bootstrap->bootWebApplication()
	->getByType(\Contributte\Console\Application::class)
	->run());