<?php

require __DIR__ . '/../vendor/autoload.php';

$bootstrap = new \App\Bootstrap;
exit($bootstrap->bootWebApplication()
	->getByType(\Contributte\Console\Application::class)
	->run());