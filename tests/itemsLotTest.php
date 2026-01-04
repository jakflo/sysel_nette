<?php

require_once 'bootstrap.php';
use tests\Classes\ItemsLotTestClass;

createContainer()->getByType(ItemsLotTestClass::class)->run();
