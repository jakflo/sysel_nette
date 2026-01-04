<?php

require_once 'bootstrap.php';
use tests\Classes\ItemsInWarehouseListTestClass;

createContainer()->getByType(ItemsInWarehouseListTestClass::class)->run();
