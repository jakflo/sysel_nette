<?php

use \App\UI\Tests\TestModelsFactory;

require __DIR__ . '/../vendor/autoload.php';
$bootstrap = new \App\Bootstrap;
$di = $bootstrap->bootWebApplication();
\Tester\Environment::setup();

$factory = $di->getByType(TestModelsFactory::class);

echo '---------Warehouses tests---------';
$factory->getWarehouseList()->run();
echo '<br /><br />---------Items tests---------';
$factory->getItemsList()->run();
echo '<br /><br />---------Items in warehouse tests---------';
$factory->getItemsInWarehouseList()->run();
echo '<br /><br />---------Items lot tests---------';
$factory->getItemsLot()->run();
