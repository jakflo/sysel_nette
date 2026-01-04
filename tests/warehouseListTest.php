<?php

require_once 'bootstrap.php';
use tests\Classes\WarehouseListTestClass;

createContainer()->getByType(WarehouseListTestClass::class)->run();
